<?php

namespace App\Listeners;

use App\Events\InvoicePaymentCreated;
use App\Notifications\InvoicePaymentReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendInvoicePaymentCreatedNotifications implements ShouldQueue
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
     * @param \App\Events\InvoicePaymentCreated $event
     * @return void
     */
    public function handle(InvoicePaymentCreated $event)
    {
        $invoicePayment = $event->invoicePayment;
        if (!empty($invoicePayment->patient->email)) {
            Notification::route('mail', $invoicePayment->patient->email)->notify(new InvoicePaymentReceived($invoicePayment));
        }
    }
}
