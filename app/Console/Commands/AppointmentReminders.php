<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\AppointmentReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class AppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send appointment reminders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $appointments = Event::with(['patient', 'branch', 'doctor'])
            ->where('status', 'approved')
            ->where('start_date', Carbon::today())
            ->where('appointment_start_date', '>', Carbon::today())
            ->get();
        foreach ($appointments as $appointment) {
            if ($appointment->patient->email) {
                Notification::route('email', $appointment->patient->email)->notify(new AppointmentReminderNotification($appointment));
            }
        }
        return 0;
    }
}
