<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Booking $booking,
        private string $message = 'Your reservation has been confirmed.'
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return $this->payload($notifiable);
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->payload($notifiable);
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->payload($notifiable));
    }

    private function payload(object $notifiable): array
    {
        $property = $this->booking->property;
        $actionUrl = match (true) {
            method_exists($notifiable, 'isOwner') && $notifiable->isOwner() => route('owner.bookings'),
            method_exists($notifiable, 'isAdmin') && $notifiable->isAdmin() => route('admin.bookings'),
            default => route('bookings.index'),
        };

        return [
            'title' => 'Reservation confirmed',
            'message' => "{$this->message} {$property?->title} is reserved.",
            'type' => 'payment_confirmation',
            'action_url' => $actionUrl,
            'metadata' => [
                'booking_id' => $this->booking->id,
                'property_id' => $property?->id,
                'property_title' => $property?->title,
                'total_price' => $this->booking->total_price,
                'status' => $this->booking->status,
            ],
        ];
    }
}
