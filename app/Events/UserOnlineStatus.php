<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast when a user's online status changes.
 * Sent to the OTHER participant in a conversation so their UI
 * can update the status dot in real time.
 */
class UserOnlineStatus implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int    $recipientId,  // who to notify
        public readonly int    $userId,       // whose status changed
        public readonly bool   $isOnline,
        public readonly string $lastSeenText,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("chat.{$this->recipientId}")];
    }

    public function broadcastAs(): string
    {
        return 'user.status';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id'       => $this->userId,
            'is_online'     => $this->isOnline,
            'last_seen_text' => $this->lastSeenText,
        ];
    }
}