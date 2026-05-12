<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class MatchingPropertyNotification extends Notification
{
    use Queueable;

    public function __construct(private Property $property) {}

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
        return [
            'title' => 'New boarding house match',
            'message' => "{$this->property->title} is now available in {$this->property->address}.",
            'type' => 'matching_property',
            'action_url' => route('property.show', $this->property->id),
            'metadata' => [
                'property_id' => $this->property->id,
                'property_title' => $this->property->title,
                'address' => $this->property->address,
                'price' => $this->property->price,
                'room_type' => $this->property->room_type,
            ],
        ];
    }
}
