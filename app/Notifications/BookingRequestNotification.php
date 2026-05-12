<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class BookingRequestNotification extends Notification
{
    use Queueable;

    public function __construct(private Booking $booking) {}

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
        $property = $this->booking->property;
        $tenant = $this->booking->user;

        return [
            'title' => 'New booking request',
            'message' => "{$tenant?->name} requested to book {$property?->title}.",
            'type' => 'booking_request',
            'action_url' => route('owner.bookings'),
            'metadata' => [
                'booking_id' => $this->booking->id,
                'property_id' => $property?->id,
                'property_title' => $property?->title,
                'tenant_id' => $tenant?->id,
                'tenant_name' => $tenant?->name,
                'status' => $this->booking->status,
            ],
        ];
    }
}
