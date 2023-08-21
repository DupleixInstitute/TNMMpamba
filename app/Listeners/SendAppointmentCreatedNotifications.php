<?php

namespace App\Listeners;

use App\Events\AppointmentCreated;
use App\Models\Setting;
use App\Notifications\AppointmentCreatedAdminNotification;
use App\Notifications\AppointmentCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendAppointmentCreatedNotifications implements ShouldQueue
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
     * @param \App\Events\AppointmentCreated $event
     * @return void
     */
    public function handle(AppointmentCreated $event)
    {
        $appointment = $event->appointment;
        if ($appointment->patient->email) {
            Notification::route('mail', $appointment->patient->email)->notify(new AppointmentCreatedNotification($appointment));
        }
        if ($appointment->created_by_type === 'patient') {
            Notification::route('mail', Setting::where('setting_key', 'company_email')->first()->setting_value)->notify(new AppointmentCreatedAdminNotification($appointment));
        }

    }
}
