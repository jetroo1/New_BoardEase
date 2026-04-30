<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast to the SENDER when the recipient has seen their messages.
 * The sender's UI will upgrade the grey ✓✓ to teal ✓✓ (seen).
 */
class MessageRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $conversationId,
        public readonly int $senderId,      // person whose messages were read
        public readonly int $readerId,      // person who just read them
    ) {}

    public function broadcastOn(): array
    {
        // Notify the original sender that their message was seen
        return [new PrivateChannel("chat.{$this->senderId}")];
    }

    public function broadcastAs(): string
    {
        return 'message.read';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'reader_id'       => $this->readerId,
        ];
    }
}