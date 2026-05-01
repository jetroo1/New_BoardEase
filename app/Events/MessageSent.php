<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int    $conversationId,
        public readonly int    $recipientId,
        public readonly int    $senderId,
        public readonly string $senderName,
        public readonly string $initials,
        public readonly string $content,
        public readonly string $time,
        public readonly string $color,
        public readonly ?string $imageUrl = null,
        public readonly ?string $avatarUrl = null,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("chat.{$this->recipientId}")];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'sender_id'       => $this->senderId,
            'sender_name'     => $this->senderName,
            'initials'        => $this->initials,
            'content'         => $this->content,
            'time'            => $this->time,
            'color'           => $this->color,
            'image_url'       => $this->imageUrl,
            'avatar_url'      => $this->avatarUrl, 
        ];
    }
}