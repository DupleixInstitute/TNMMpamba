<?php

namespace App\Console\Commands;

use App\Events\AppointmentStatusChanged;
use App\Models\Event;
use App\Notifications\AppointmentReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

class UpdateAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates missed appointments';

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
            ->where('start_date', Carbon::today()->subDay()->format('Y-m-d'))
            ->where('appointment_start_date', '<', Carbon::today())
            ->get();
        foreach ($appointments as $appointment) {
            $appointment->save = 1;
            $appointment->save();
            event(new AppointmentStatusChanged($appointment));
        }
        return 0;
    }
}
