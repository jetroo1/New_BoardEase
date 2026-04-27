<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebRTCSignal implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int    $recipientId,
        public readonly int    $senderId,
        public readonly string $senderName,
        public readonly string $type,
        public readonly array  $payload,          // ← changed: typed as array (not mixed)
        public readonly int    $conversationId,   // ← added: needed by callee to route answer
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("chat.{$this->recipientId}")];
    }

    public function broadcastAs(): string
    {
        return 'webrtc.signal';
    }

    public function broadcastWith(): array
    {
        // Pass payload as-is — NO re-encoding. Re-encoding corrupts SDP strings.
        return [
            'conversation_id' => $this->conversationId,   // ← added
            'sender_id'       => $this->senderId,
            'sender_name'     => $this->senderName,
            'type'            => $this->type,
            'payload'         => $this->payload,
        ];
    }
}