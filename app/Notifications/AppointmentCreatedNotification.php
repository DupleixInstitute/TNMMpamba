<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\CommunicationTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCreatedNotification extends Notification implements ShouldQueue
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
        $appointment = $this->appointment;
        $template = CommunicationTemplate::where('system_name', 'appointment_created_email_template')->first();
        $body = template_replace_tags([
            'body' => $template->description,
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'user_id' => $appointment->doctor_id,
        ]);
        $email = (new MailMessage)
            ->subject($template->subject)->markdown('emails.basic_email', ['body' => $body]);

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
