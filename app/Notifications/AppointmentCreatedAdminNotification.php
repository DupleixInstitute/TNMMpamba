<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCreatedAdminNotification extends Notification implements ShouldQueue
{

    use Queueable;

    public Event $appointment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Event $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Appointment Created')
            ->line('A new appointment has been made by ' . $this->appointment->patient->name . '.')
            ->line('Appointment Date:' . $this->appointment->start_date . ' ' . $this->appointment->start_time)
            ->line('Doctor:' . $this->appointment->doctor->name ?? '')
            ->action('View Appointment', route('appointments.show', $this->appointment->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
