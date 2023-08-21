<?php

namespace App\Listeners;

use App\Events\AppointmentAssigned;
use App\Notifications\DoctorAppointmentAssignedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAppointmentAssignedNotifications implements ShouldQueue
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
     * @param \App\Events\AppointmentAssigned $event
     * @return void
     */
    public function handle(AppointmentAssigned $event)
    {
        $appointment = $event->appointment;
        $appointment->doctor->notify(new DoctorAppointmentAssignedNotification($appointment));
    }
}
