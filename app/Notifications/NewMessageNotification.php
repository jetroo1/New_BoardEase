<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Message $message,
        private Conversation $conversation,
        private User $sender
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return $this->payload();
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->payload();
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->payload());
    }

    private function payload(): array
    {
        $preview = $this->message->image_path
            ? 'sent you an image.'
            : 'sent you: "' . Str::limit($this->message->content, 70) . '"';

        return [
            'title' => 'New message',
            'message' => "{$this->sender->name} {$preview}",
            'type' => 'message',
            'action_url' => route('chat', ['open' => $this->conversation->id]),
            'metadata' => [
                'conversation_id' => $this->conversation->id,
                'message_id' => $this->message->id,
                'sender_id' => $this->sender->id,
                'sender_name' => $this->sender->name,
            ],
        ];
    }
}
