<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class PropertyUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(private Property $property, private array $changedFields = []) {}

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
        $fields = collect($this->changedFields)
            ->map(fn ($field) => str_replace('_', ' ', $field))
            ->join(', ');

        return [
            'title' => 'Property updated',
            'message' => $fields
                ? "{$this->property->title} updated: {$fields}."
                : "{$this->property->title} has updated details.",
            'type' => 'property_updated',
            'action_url' => route('property.show', $this->property->id),
            'metadata' => [
                'property_id' => $this->property->id,
                'property_title' => $this->property->title,
                'changed_fields' => $this->changedFields,
            ],
        ];
    }
}
