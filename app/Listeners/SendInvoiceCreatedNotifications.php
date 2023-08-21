<?php

namespace App\Listeners;

use App\Events\InvoiceCreated;
use App\Notifications\InvoiceGeneratedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendInvoiceCreatedNotifications implements ShouldQueue
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
     * @param \App\Events\InvoiceCreated $event
     * @return void
     */
    public function handle(InvoiceCreated $event)
    {
        $invoice = $event->invoice;
        if (!empty($invoice->patient->email)) {
            Notification::route('mail', $invoice->patient->email)->notify(new InvoiceGeneratedNotification($invoice));
        }
    }
}
