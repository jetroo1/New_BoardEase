<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\AdminAnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->expectsJson() ? 20 : 100;
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn (DatabaseNotification $notification) => $this->formatNotification($notification))
            ->values();

        $payload = [
            'notifications' => $notifications,
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ];

        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        return view('notifications.index', $payload);
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'ok' => true,
            'notification' => $this->formatNotification($notification->fresh()),
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'ok' => true,
            'unread_count' => 0,
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json([
            'ok' => true,
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function broadcastAnnouncement(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:1000'],
            'action_url' => ['nullable', 'url'],
        ]);

        User::query()->chunkById(100, function ($users) use ($data) {
            $users->each(fn (User $user) => $user->notify(new AdminAnnouncementNotification(
                $data['title'],
                $data['message'],
                $data['action_url'] ?? null,
                ['sent_by' => 'admin']
            )));
        });

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Announcement sent to all users.');
    }

    private function formatNotification(DatabaseNotification $notification): array
    {
        $data = $notification->data ?? [];
        $type = $data['type'] ?? 'system';

        return [
            'id' => $notification->id,
            'title' => $data['title'] ?? 'BoardEase update',
            'message' => $data['message'] ?? 'You have a new notification.',
            'type' => $type,
            'category' => $this->categoryForType($type),
            'action_url' => $data['action_url'] ?? route('notifications'),
            'metadata' => $data['metadata'] ?? [],
            'read_at' => $notification->read_at?->toIso8601String(),
            'created_at' => $notification->created_at?->toIso8601String(),
            'time_ago' => $notification->created_at?->diffForHumans() ?? '',
            'unread' => is_null($notification->read_at),
            'icon' => $this->iconForType($type),
            'tone' => $this->toneForType($type),
        ];
    }

    private function iconForType(string $type): string
    {
        return match ($type) {
            'message' => 'fas fa-comment-dots',
            'booking_request' => 'fas fa-calendar-plus',
            'booking_status' => 'fas fa-calendar-check',
            'review' => 'fas fa-star',
            'matching_property' => 'fas fa-house-chimney',
            'property_updated' => 'fas fa-pen-to-square',
            'payment_confirmation' => 'fas fa-receipt',
            'admin_announcement' => 'fas fa-bullhorn',
            default => 'fas fa-bell',
        };
    }

    private function toneForType(string $type): string
    {
        return match ($type) {
            'message' => 'message',
            'booking_request', 'booking_status' => 'booking',
            'review' => 'review',
            'matching_property', 'property_updated' => 'property',
            'payment_confirmation' => 'payment',
            'admin_announcement' => 'system',
            default => 'system',
        };
    }

    private function categoryForType(string $type): string
    {
        return match ($type) {
            'message' => 'messages',
            'booking_request', 'booking_status', 'payment_confirmation' => 'bookings',
            'review', 'matching_property', 'property_updated' => 'properties',
            default => 'system',
        };
    }
}
