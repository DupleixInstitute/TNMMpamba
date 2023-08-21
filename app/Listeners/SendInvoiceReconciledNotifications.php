<?php

namespace App\Listeners;

use App\Events\InvoiceReconciled;
use App\Notifications\InvoiceShortfallNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendInvoiceReconciledNotifications implements ShouldQueue
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
     * @param \App\Events\InvoiceReconciled $event
     * @return void
     */
    public function handle(InvoiceReconciled $event)
    {
        $invoice = $event->invoice;
        if ($invoice->shortfall > 0) {
            if ($invoice->patient->email) {
                Notification::route('mail', $invoice->patient->email)->notify(new InvoiceShortfallNotification($invoice));
            }
        }
    }
}
