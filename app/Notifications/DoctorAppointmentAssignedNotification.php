<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DoctorAppointmentAssignedNotification extends Notification implements ShouldQueue
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
        if ($this->appointment->appointment_type === 'in_clinic') {
            $appointmentType = 'In-clinic';
        }
        if ($this->appointment->appointment_type === 'video') {
            $appointmentType = 'Video';
        }
        if ($this->appointment->appointment_type === 'text') {
            $appointmentType = 'Text';
        }
        $email = (new MailMessage)
            ->subject('Appointment Assigned')
            ->line('A new appointment has been assigned to you.');
        $email->line('Patient:' . $this->appointment->patient->name . '.')
            ->line('Appointment Date:' . $this->appointment->start_date . ' ' . $this->appointment->start_time)
            ->line('Appointment Type:' . $appointmentType)
            ->line('Reason:' . $this->appointment->reason)
            ->line('Additional Notes:' . $this->appointment->description)
            ->action('View Appointment', route('appointments.show', $this->appointment->id))
            ->line('Thank you for using our application!');
        return $email;
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
