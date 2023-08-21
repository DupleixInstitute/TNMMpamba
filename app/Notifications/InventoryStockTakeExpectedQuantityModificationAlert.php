<?php

namespace App\Notifications;

use App\Models\InventoryStockTake;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InventoryStockTakeExpectedQuantityModificationAlert extends Notification
{
    use Queueable;

    public InventoryStockTake $stockTake;
    public $modifiedCount;
    public $quantityExpected;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(InventoryStockTake $stockTake,$quantityExpected, $modifiedCount)
    {
        $this->stockTake = $stockTake;
        $this->modifiedCount = $modifiedCount;
        $this->quantityExpected = $quantityExpected;
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
        return (new MailMessage)
            ->line('The Expected Column for stock take ' . $this->stockTake->date . ' was modified.')
            ->line('Expected Count ' . $this->quantityExpected)
            ->line('Modified Count ' . $this->modifiedCount)
            ->action('View Stock Take', route('inventory.stock_takes.show', $this->stockTake->id))
            ->line('Thank you for using our application!');
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
