<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Events\UserTyping;
use App\Events\UserOnlineStatus;
use App\Events\WebRTCSignal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    // Consistent avatar color per user id
    private function colorForUser(User $user): string
    {
        $colors = ['#2ec4a5','#8b5cf6','#f59e0b','#3b82f6','#ec4899','#ef4444','#10b981','#f97316'];
        return $colors[$user->id % count($colors)];
    }

    /**
     * Show the chat page.
     * Also updates the user's last_seen_at so others see them as online.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Update online status
        $user->update(['last_seen_at' => now()]);

        $conversations = Conversation::where('participant_one', $user->id)
            ->orWhere('participant_two', $user->id)
            ->with(['participantOne', 'participantTwo', 'lastMessage.sender'])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($conv) use ($user) {
                $other  = $conv->getOtherParticipant($user->id);
                $unread = $conv->unreadCount($user->id);
                $last   = $conv->lastMessage;
                return [
                    'id'        => $conv->id,
                    'other'     => $other,
                    'color'     => $this->colorForUser($other),
                    'initials'  => strtoupper(substr($other->name, 0, 1)),
                    'avatar_url' => $other->avatar_url, 
                    'preview'   => $last
                        ? ($last->image_path ? '📷 Image' : \Str::limit($last->content, 40))
                        : 'Start a conversation',
                    'time'      => $last ? $last->created_at->diffForHumans(null, true) . ' ago' : '',
                    'unread'    => $unread,
                    'is_online' => $other->isOnline(),
                    'last_seen' => $other->lastSeenText(),
                ];
            });

        $allUsers = User::where('id', '!=', $user->id)->get()     
        ->map(fn($u) => [
         'id'         => $u->id,
         'name'       => $u->name,
         'role'       => $u->role,
         'color'      => $this->colorForUser($u),
         'initials'   => strtoupper(substr($u->name, 0, 1)),
         'avatar_url' => $u->avatar_url,
     ]);

        return view('chat.index', [
            'user'          => $user,
            'userColor'     => $this->colorForUser($user),
            'userInitials'  => strtoupper(substr($user->name, 0, 1)),
            'conversations' => $conversations,
            'allUsers'      => $allUsers,
        ]);
    }

    /**
     * Redirect to chat page with conversation open.
     */
    public function show(Request $request, $conversationId)
    {
        $user = Auth::user();
        $conv = Conversation::findOrFail($conversationId);
        abort_unless($conv->hasParticipant($user->id), 403);

        return redirect()->route('chat', ['open' => $conversationId]);
    }

    /**
     * Start or open a conversation with another user.
     */
    public function startOrOpen(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $me    = Auth::id();
        $other = (int) $request->user_id;

        if ($me === $other) {
            return response()->json(['error' => 'Cannot chat with yourself'], 422);
        }

        $conv = Conversation::where(function ($q) use ($me, $other) {
            $q->where('participant_one', $me)->where('participant_two', $other);
        })->orWhere(function ($q) use ($me, $other) {
            $q->where('participant_one', $other)->where('participant_two', $me);
        })->first();

        if (!$conv) {
            $conv = Conversation::create([
                'participant_one' => $me,
                'participant_two' => $other,
                'last_message_at' => now(),
            ]);
        }

        return response()->json(['conversation_id' => $conv->id]);
    }

    /**
     * Load messages with pagination (20 per page, scroll up loads more).
     * GET /chat/{conversation}/messages?page=1&per_page=20
     */
    public function getMessages(Request $request, $conversationId)
    {
        $user = Auth::user();
        $conv = Conversation::findOrFail($conversationId);
        abort_unless($conv->hasParticipant($user->id), 403);

        // Mark all incoming messages as read
        $unreadMessages = Message::where('conversation_id', $conv->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->get();

        if ($unreadMessages->isNotEmpty()) {
            Message::where('conversation_id', $conv->id)
                ->where('sender_id', '!=', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            // Tell the sender their messages were seen
            $other = $conv->getOtherParticipant($user->id);
            broadcast(new MessageRead(
                conversationId: $conv->id,
                senderId:       $other->id,
                readerId:       $user->id,
            ))->toOthers();
        }

        // Paginate: newest messages last, load older ones by page
        $perPage = (int) $request->get('per_page', 30);
        $page    = (int) $request->get('page', 1);

        $total    = Message::where('conversation_id', $conv->id)->count();
        $messages = Message::where('conversation_id', $conv->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'messages'     => $messages->map(fn($m) => $this->formatMessage($m, $user->id)),
            'has_more'     => $messages->hasMorePages() === false && $page > 1 ? false : $page < $messages->lastPage(),
            'current_page' => $page,
            'last_page'    => $messages->lastPage(),
            'total'        => $total,
        ]);
    }

    /**
     * Mark messages in a conversation as seen.
     * POST /chat/{conversation}/seen
     */
    public function markSeen(Request $request, $conversationId)
    {
        $user = Auth::user();
        $conv = Conversation::findOrFail($conversationId);
        abort_unless($conv->hasParticipant($user->id), 403);

        $updated = Message::where('conversation_id', $conv->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($updated > 0) {
            $other = $conv->getOtherParticipant($user->id);
            broadcast(new MessageRead(
                conversationId: $conv->id,
                senderId:       $other->id,
                readerId:       $user->id,
            ))->toOthers();
        }

        return response()->json(['ok' => true, 'marked' => $updated]);
    }

    /**
     * Send a text or image message.
     * POST /chat/{conversation}/messages
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'content' => 'nullable|string|max:5000',
            'image'   => 'nullable|image|max:5120', // max 5MB
        ]);

        // Must have either content or image
        if (!$request->filled('content') && !$request->hasFile('image')) {
            return response()->json(['error' => 'Message content or image required'], 422);
        }

        $user = Auth::user();
        $conv = Conversation::findOrFail($conversationId);
        abort_unless($conv->hasParticipant($user->id), 403);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat-images', 'public');
        }

        $message = Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user->id,
            'content'         => $request->content ?? '',
            'image_path'      => $imagePath,
        ]);

        $conv->update(['last_message_at' => now()]);

        $recipient = $conv->getOtherParticipant($user->id);

        broadcast(new MessageSent(
            conversationId: $conv->id,
            recipientId:    $recipient->id,
            senderId:       $user->id,
            senderName:     $user->name,
            initials:       strtoupper(substr($user->name, 0, 1)),
            content:        $request->content ?? '',
            time:           $message->created_at->format('h:i A'),
            color:          $this->colorForUser($user),
            imageUrl:       $message->image_url,   // ← FIX: pass image URL to receiver
            avatarUrl:      $user->avatar_url, 
        ))->toOthers();

        return response()->json([
            'id'        => $message->id,
            'content'   => $message->content,
            'image_url' => $message->image_url,
            'time'      => $message->created_at->format('h:i A'),
        ]);
    }

    /**
     * Broadcast typing indicator.
     */
    public function typing(Request $request, $conversationId)
    {
        $user = Auth::user();
        $conv = Conversation::findOrFail($conversationId);
        abort_unless($conv->hasParticipant($user->id), 403);

        // Update last_seen while typing too
        $user->update(['last_seen_at' => now()]);

        $recipient = $conv->getOtherParticipant($user->id);
        broadcast(new UserTyping($recipient->id, $user->name))->toOthers();

        return response()->json(['ok' => true]);
    }

    /**
     * Update online status (called via heartbeat from frontend every 60s).
     * POST /chat/heartbeat
     */
    public function heartbeat(Request $request)
    {
        $user = Auth::user();
        $user->update(['last_seen_at' => now()]);

        // Notify all conversations' other participants of online status
        $conversations = Conversation::where('participant_one', $user->id)
            ->orWhere('participant_two', $user->id)
            ->get();

        foreach ($conversations as $conv) {
            $other = $conv->getOtherParticipant($user->id);
            broadcast(new UserOnlineStatus(
                recipientId:  $other->id,
                userId:       $user->id,
                isOnline:     true,
                lastSeenText: 'Online',
            ))->toOthers();
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Relay a WebRTC signaling message (kept for backward compatibility).
     */
    public function signal(Request $request, $conversationId)
    {
        $request->validate([
            'type' => 'required|string|in:offer,answer,ice-candidate,call-rejected,call-ended',
        ]);

        $user = Auth::user();
        $conv = Conversation::findOrFail($conversationId);
        abort_unless($conv->hasParticipant($user->id), 403);

        $recipient = $conv->getOtherParticipant($user->id);

        $payload = $request->input('payload', []);
        if (!is_array($payload)) {
            $payload = (array) $payload;
        }

        broadcast(new WebRTCSignal(
            recipientId:    $recipient->id,
            senderId:       $user->id,
            senderName:     $user->name,
            type:           $request->type,
            payload:        $payload,
            conversationId: $conv->id,
        ))->toOthers();

        return response()->json(['ok' => true]);
    }

    // ── Private helper ────────────────────────────────────────
    private function formatMessage(Message $m, int $myId): array
    {
        return [
            'id'        => $m->id,
            'content'   => $m->content,
            'image_url' => $m->image_url,
            'sent'      => $m->sender_id === $myId,
            'initials'  => strtoupper(substr($m->sender->name, 0, 1)),
            'color'     => $this->colorForUser($m->sender),
            'avatar_url' => $m->sender->avatar_url,
            'time'      => $m->created_at->format('h:i A'),
            'created_at' => $m->created_at->toIso8601String(),
            'read'      => $m->isRead(),
            'seen_at'   => $m->read_at?->toIso8601String(),
        ];
    }
}