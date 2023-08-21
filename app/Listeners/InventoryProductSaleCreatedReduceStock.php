<?php

namespace App\Listeners;

use App\Events\InventoryProductSaleCreated;
use App\Models\InventoryProductStock;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class InventoryProductSaleCreatedReduceStock implements ShouldQueue
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
     * @param \App\Events\InventoryProductSaleCreated $event
     * @return void
     */
    public function handle(InventoryProductSaleCreated $event)
    {
        $sale = $event->sale;
        if ($sale->status === 'completed') {
            //decrease stock for variation item
            $accountingMethod = Setting::where('setting_key', 'inventory_stock_accounting_method')->first()->setting_value;
            $sale->items->each(function ($item) use ($sale, $accountingMethod) {
                $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $sale->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->where('quantity', '>', 0);
                if ($accountingMethod === 'FIFO (First In First Out)' || empty($accountingMethod)) {
                    $inventoryStock->orderBy('created_at');
                }
                if ($accountingMethod === 'LIFO (Last In First Out)') {
                    $inventoryStock->orderBy('created_at', 'desc');
                }
                $inventoryStock = $inventoryStock->get();
                $saleQuantity = $item->quantity;
                foreach ($inventoryStock as $value) {
                    if ($value->quantity >= $saleQuantity) {
                        $value->quantity = $value->quantity - $saleQuantity;
                        $saleQuantity = 0;
                    } else {
                        $saleQuantity = $saleQuantity - $value->quantity;
                        $value->quantity = 0;
                    }
                    $value->quantity_out = abs($value->quantity_in - $value->quantity);
                    $value->stock_value = $value->quantity * $value->unit_cost;
                    $value->save();
                    if ($saleQuantity === 0) {
                        break;
                    }
                }
            });

        }
    }
}
