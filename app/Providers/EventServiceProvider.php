<?php

namespace App\Providers;

use App\Events\AppointmentAssigned;
use App\Events\AppointmentCreated;
use App\Events\AppointmentStatusChanged;
use App\Events\ClaimBatchClosed;
use App\Events\ClaimBatchSent;
use App\Events\ClaimSent;
use App\Events\CommunicationCampaignSent;
use App\Events\ConsultationCreated;
use App\Events\ConsultationDoctorCompleted;
use App\Events\ConsultationNurseCompleted;
use App\Events\ConsultationReceptionCompleted;
use App\Events\InventoryProductPurchaseCreated;
use App\Events\InventoryProductPurchaseReturnCreated;
use App\Events\InventoryProductSaleCreated;
use App\Events\InventoryProductSaleDeleted;
use App\Events\InventoryProductSaleReturnCreated;
use App\Events\InventoryProductSaleReturnDeleted;
use App\Events\InventoryProductSaleReturnUpdated;
use App\Events\InventoryProductSaleUpdated;
use App\Events\InventoryStockAdjustmentCreated;
use App\Events\InventoryStockAdjustmentDeleted;
use App\Events\InventoryStockAdjustmentUpdated;
use App\Events\InventoryStockTakeCreated;
use App\Events\InvoiceClaimed;
use App\Events\InvoiceCreated;
use App\Events\InvoicePaymentCreated;
use App\Events\InvoiceReconciled;
use App\Events\StartVideoConsultation;
use App\Listeners\InventoryProductSaleCreatedReduceStock;
use App\Listeners\InventoryProductSaleDeletedUpdateStock;
use App\Listeners\InventoryProductSaleReturnCreatedUpdateStock;
use App\Listeners\InventoryProductSaleReturnDeletedUpdateStock;
use App\Listeners\InventoryProductSaleReturnUpdatedUpdateStock;
use App\Listeners\InventoryProductSaleUpdatedUpdateStock;
use App\Listeners\InventoryStockAdjustmentCreatedUpdateStock;
use App\Listeners\InventoryStockAdjustmentDeletedUpdateStock;
use App\Listeners\InventoryStockAdjustmentUpdatedUpdateStock;
use App\Listeners\InventoryStockTakeCreatedCreateStockTakeItems;
use App\Listeners\SendAppointmentAssignedNotifications;
use App\Listeners\SendAppointmentCreatedNotifications;
use App\Listeners\SendAppointmentStatusChangedNotifications;
use App\Listeners\SendInvoiceCreatedNotifications;
use App\Listeners\SendInvoicePaymentCreatedNotifications;
use App\Listeners\SendInvoiceReconciledNotifications;
use App\Listeners\UpdateUserLastLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Login::class => [
            UpdateUserLastLogin::class,
        ],
        InvoiceCreated::class => [
            SendInvoiceCreatedNotifications::class,
        ],
        InvoicePaymentCreated::class => [
            SendInvoicePaymentCreatedNotifications::class,
        ],
        ClaimBatchClosed::class => [

        ],
        ClaimBatchSent::class => [

        ],
        ClaimSent::class => [

        ],
        InvoiceReconciled::class => [
            SendInvoiceReconciledNotifications::class,
        ],
        AppointmentCreated::class => [
            SendAppointmentCreatedNotifications::class,
        ],
        AppointmentStatusChanged::class => [
            SendAppointmentStatusChangedNotifications::class,
        ],
        AppointmentAssigned::class => [
            SendAppointmentAssignedNotifications::class,
        ],
        ConsultationCreated::class => [

        ],
        InventoryProductSaleCreated::class => [
            InventoryProductSaleCreatedReduceStock::class,
        ],
        InventoryProductSaleUpdated::class => [
            InventoryProductSaleUpdatedUpdateStock::class,
        ],
        InventoryProductSaleDeleted::class => [
            InventoryProductSaleDeletedUpdateStock::class,
        ],
        InventoryProductPurchaseCreated::class => [

        ],
        InventoryProductPurchaseReturnCreated::class => [

        ],
        InventoryProductSaleReturnCreated::class => [
            InventoryProductSaleReturnCreatedUpdateStock::class
        ],
        InventoryProductSaleReturnUpdated::class => [
            InventoryProductSaleReturnUpdatedUpdateStock::class
        ],
        InventoryProductSaleReturnDeleted::class => [
            InventoryProductSaleReturnDeletedUpdateStock::class
        ],
        InventoryStockTakeCreated::class => [
            InventoryStockTakeCreatedCreateStockTakeItems::class,
        ],
        InventoryStockAdjustmentCreated::class => [
            InventoryStockAdjustmentCreatedUpdateStock::class,
        ],
        InventoryStockAdjustmentUpdated::class => [
            InventoryStockAdjustmentUpdatedUpdateStock::class,
        ],
        InventoryStockAdjustmentDeleted::class => [
            InventoryStockAdjustmentDeletedUpdateStock::class,
        ],
        InvoiceClaimed::class => [

        ],
        StartVideoConsultation::class => [

        ],
        ConsultationDoctorCompleted::class => [

        ],
        ConsultationNurseCompleted::class => [

        ],
        ConsultationReceptionCompleted::class => [

        ],
        CommunicationCampaignSent::class => [

        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
