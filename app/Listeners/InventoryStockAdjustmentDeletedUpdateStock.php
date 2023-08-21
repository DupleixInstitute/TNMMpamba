<?php

namespace App\Listeners;

use App\Events\InventoryStockAdjustmentDeleted;
use App\Models\InventoryProductStock;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class InventoryStockAdjustmentDeletedUpdateStock implements ShouldQueue
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
     * @param \App\Events\InventoryStockAdjustmentDeleted $event
     * @return void
     */
    public function handle(InventoryStockAdjustmentDeleted $event)
    {
        $adjustment = $event->adjustment;
        $accountingMethod = Setting::where('setting_key', 'inventory_stock_accounting_method')->first()->setting_value;
        $adjustment->each(function ($item) use ($adjustment, $accountingMethod) {

            if ($item->stock_adjustment_type === 'addition') {
                $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $adjustment->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->where('quantity', '>', 0);
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
            } else {
                $inventoryStock = new InventoryProductStock();
                $inventoryStock->inventory_warehouse_id = $adjustment->inventory_warehouse_id;
                $inventoryStock->inventory_product_id = $item->inventory_product_id;
                $inventoryStock->inventory_product_variation_id = $item->inventory_product_variation_id;
                $inventoryStock->quantity = $item->quantity;
                $inventoryStock->quantity_in = $item->quantity;
                $inventoryStock->stock_value = $item->quantity * $item->purchase_price;
                $inventoryStock->unit_cost = $item->purchase_price;
                $inventoryStock->average_unit_cost = $item->purchase_price;
                $inventoryStock->save();
            }

        });
    }
}
