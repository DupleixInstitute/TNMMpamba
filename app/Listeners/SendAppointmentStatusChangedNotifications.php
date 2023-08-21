<?php

namespace App\Listeners;

use App\Events\AppointmentStatusChanged;
use App\Notifications\AppointmentStatusChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendAppointmentStatusChangedNotifications implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param \App\Events\AppointmentStatusChanged $event
     * @return void
     */
    public function handle(AppointmentStatusChanged $event)
    {
        $appointment = $event->appointment;
        if (in_array($appointment->status, ['approved', 'declined', 'cancelled','missed']) && $appointment->patient->email) {
            Notification::route('mail', $appointment->patient->email)->notify(new AppointmentStatusChangedNotification($appointment));
        }
    }
}
