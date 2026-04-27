<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Events\UserTyping;
use App\Events\WebRTCSignal;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Avatar color palette — consistent per user id
    private function colorForUser(User $user): string
    {
        $colors = ['#2ec4a5','#8b5cf6','#f59e0b','#3b82f6','#ec4899','#ef4444','#10b981','#f97316'];
        return $colors[$user->id % count($colors)];
    }

    /**
     * Show the chat page with all conversations for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

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
                    'id'       => $conv->id,
                    'other'    => $other,
                    'color'    => $this->colorForUser($other),
                    'initials' => strtoupper(substr($other->name, 0, 1)),
                    'preview'  => $last ? \Str::limit($last->content, 40) : 'Start a conversation',
                    'time'     => $last ? $last->created_at->diffForHumans(null, true) . ' ago' : '',
                    'unread'   => $unread,
                ];
            });

        // All users except self (for new conversation modal)
        $allUsers = User::where('id', '!=', $user->id)->select('id','name','role')->get()
            ->map(fn($u) => array_merge($u->toArray(), [
                'color'    => $this->colorForUser($u),
                'initials' => strtoupper(substr($u->name, 0, 1)),
            ]));

        return view('chat.index', [
            'user'         => $user,
            'userColor'    => $this->colorForUser($user),
            'userInitials' => strtoupper(substr($user->name, 0, 1)),
            'conversations' => $conversations,
            'allUsers'     => $allUsers,
        ]);
    }

    /**
     * Start or open a conversation with another user.
     */
    public function show(Request $request, $conversationId)
    {
        $user = Auth::user();
        $conv = Conversation::findOrFail($conversationId);

        abort_unless($conv->hasParticipant($user->id), 403);

        Message::where('conversation_id', $conv->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('chat', ['open' => $conversationId]);
    }

    /**
     * Get or create a conversation with a user, then redirect to chat.
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
     * Load messages for a conversation (AJAX).
     */
    public function getMessages(Request $request, $conversationId)
    {
        $user = Auth::user();
        $conv = Conversation::findOrFail($conversationId);
        abort_unless($conv->hasParticipant($user->id), 403);

        Message::where('conversation_id', $conv->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where('conversation_id', $conv->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => [
                'id'       => $m->id,
                'content'  => $m->content,
                'sent'     => $m->sender_id === $user->id,
                'initials' => strtoupper(substr($m->sender->name, 0, 1)),
                'color'    => $this->colorForUser($m->sender),
                'time'     => $m->created_at->format('h:i A'),
                'read'     => $m->isRead(),
            ]);

        return response()->json(['messages' => $messages]);
    }

    /**
     * Send a message and broadcast via Reverb.
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate(['content' => 'required|string|max:5000']);

        $user = Auth::user();
        $conv = Conversation::findOrFail($conversationId);
        abort_unless($conv->hasParticipant($user->id), 403);

        $message = Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $user->id,
            'content'         => $request->content,
        ]);

        $conv->update(['last_message_at' => now()]);

        $recipient = $conv->getOtherParticipant($user->id);

        broadcast(new MessageSent(
            conversationId: $conv->id,
            recipientId:    $recipient->id,
            senderId:       $user->id,
            senderName:     $user->name,
            initials:       strtoupper(substr($user->name, 0, 1)),
            content:        $request->content,
            time:           $message->created_at->format('h:i A'),
            color:          $this->colorForUser($user),
        ))->toOthers();

        return response()->json([
            'id'      => $message->id,
            'content' => $message->content,
            'time'    => $message->created_at->format('h:i A'),
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

        $recipient = $conv->getOtherParticipant($user->id);
        broadcast(new UserTyping($recipient->id, $user->name))->toOthers();

        return response()->json(['ok' => true]);
    }

    /**
     * Relay a WebRTC signaling message to the other participant.
     *
     * FIX: Pass payload as plain array (no re-encoding) and include conversationId
     * so the callee knows which conversation to route their answer signal through.
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

        // Accept payload as plain array — do NOT re-encode/decode.
        // Re-encoding was corrupting SDP strings in the previous version.
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
            conversationId: $conv->id,   // ← FIX: now included
        ))->toOthers();

        return response()->json(['ok' => true]);
    }
}