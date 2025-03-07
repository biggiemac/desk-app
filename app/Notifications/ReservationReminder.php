<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationReminder extends Notification
{
    use Queueable;

    protected $reservation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reminder: Upcoming Reservation Tomorrow')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a reminder about your upcoming reservation tomorrow.')
            ->line('Date: ' . $this->reservation->date->format('l, F j, Y'))
            ->line('Time: ' . $this->reservation->time_slot . ' Session')
            ->action('View Reservation Details', url('/dashboard'))
            ->line('We look forward to seeing you!')
            ->line('If you need to cancel, please do so at least 24 hours in advance.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
