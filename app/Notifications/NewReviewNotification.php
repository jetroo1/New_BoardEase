<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification
{
    use Queueable;

    public function __construct(private Review $review) {}

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
        $property = $this->review->property;
        $reviewer = $this->review->user;

        return [
            'title' => 'New property review',
            'message' => "{$reviewer?->name} rated {$property?->title} {$this->review->rating} stars.",
            'type' => 'review',
            'action_url' => route('property.show', $property?->id),
            'metadata' => [
                'review_id' => $this->review->id,
                'property_id' => $property?->id,
                'property_title' => $property?->title,
                'reviewer_id' => $reviewer?->id,
                'reviewer_name' => $reviewer?->name,
                'rating' => $this->review->rating,
            ],
        ];
    }
}
