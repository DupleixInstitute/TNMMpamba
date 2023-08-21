<?php

namespace App\Listeners;

use App\Events\InventoryProductSaleReturnCreated;
use App\Models\InventoryProductStock;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class InventoryProductSaleReturnCreatedUpdateStock implements ShouldQueue
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
     * @param \App\Events\InventoryProductSaleReturnCreated $event
     * @return void
     */
    public function handle(InventoryProductSaleReturnCreated $event)
    {
        $saleReturn = $event->saleReturn;
        $accountingMethod = Setting::where('setting_key', 'inventory_stock_accounting_method')->first()->setting_value;
        $saleReturn->items->each(function ($item) use ($saleReturn, $accountingMethod) {
            //find the last stock
            if ($accountingMethod === 'FIFO (First In First Out)' || empty($accountingMethod)) {
                $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $saleReturn->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->where('quantity', '>', 0)->orderBy('created_at')->first();
                if (empty($inventoryStock)) {
                    //just find the last stock record
                    $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $saleReturn->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->orderBy('created_at')->first();
                }
            } elseif ($accountingMethod === 'LIFO (Last In First Out)') {
                $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $saleReturn->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->where('quantity', '>', 0)->orderBy('created_at', 'desc')->first();
                if (empty($inventoryStock)) {
                    //just find the last stock record
                    $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $saleReturn->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->orderBy('created_at', 'desc')->first();
                }
            } else {
                $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $saleReturn->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->orderBy('created_at', 'desc')->first();
            }
            if (!empty($inventoryStock)) {
                $inventoryStock->quantity = $inventoryStock->quantity + $item->return_quantity;
                $inventoryStock->quantity_out = abs($inventoryStock->quantity_in - $inventoryStock->quantity);
                $inventoryStock->stock_value = $inventoryStock->quantity * $inventoryStock->unit_cost;
                $inventoryStock->save();
            } else {
                //throw exception
            }
        });
    }
}
