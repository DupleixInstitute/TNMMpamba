<?php

namespace App\Listeners;

use App\Events\InventoryProductSaleDeleted;
use App\Models\InventoryProductStock;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class InventoryProductSaleDeletedUpdateStock implements ShouldQueue
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
     * @param \App\Events\InventoryProductSaleDeleted $event
     * @return void
     */
    public function handle(InventoryProductSaleDeleted $event)
    {
        $sale = $event->sale;
        $accountingMethod = Setting::where('setting_key', 'inventory_stock_accounting_method')->first()->setting_value;
        $sale->items->each(function ($item) use ($sale, $accountingMethod) {
            //find the last stock
            if ($accountingMethod === 'FIFO (First In First Out)' || empty($accountingMethod)) {
                $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $sale->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->where('quantity', '>', 0)->orderBy('created_at')->first();
                if (empty($inventoryStock)) {
                    //just find the last stock record
                    $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $sale->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->orderBy('created_at')->first();
                }
            } elseif ($accountingMethod === 'LIFO (Last In First Out)') {
                $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $sale->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->where('quantity', '>', 0)->orderBy('created_at', 'desc')->first();
                if (empty($inventoryStock)) {
                    //just find the last stock record
                    $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $sale->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->orderBy('created_at', 'desc')->first();
                }
            } else {
                $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $sale->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->orderBy('created_at', 'desc')->first();
            }
            if (!empty($inventoryStock)) {
                $inventoryStock->quantity = $inventoryStock->quantity + $item->quantity;
                $inventoryStock->quantity_out = abs($inventoryStock->quantity_in - $inventoryStock->quantity);
                $inventoryStock->stock_value = $inventoryStock->quantity * $inventoryStock->unit_cost;
                $inventoryStock->save();
            } else {
                //throw exception
            }
        });
    }
}
