<?php

namespace App\Listeners;

use App\Events\InventoryProductSaleReturnUpdated;
use App\Models\InventoryProductStock;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class InventoryProductSaleReturnUpdatedUpdateStock implements ShouldQueue
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
     * @param \App\Events\InventoryProductSaleReturnUpdated $event
     * @return void
     */
    public function handle(InventoryProductSaleReturnUpdated $event)
    {
        $saleReturn = $event->saleReturn;
        $accountingMethod = Setting::where('setting_key', 'inventory_stock_accounting_method')->first()->setting_value;
        $saleReturn->items->each(function ($item) use ($saleReturn, $accountingMethod) {
            if ($item->isDirty('return_quantity')) {
                //find the last stock
                if ($item->getOriginal('return_quantity') > $item->return_quantity) {
                    //increase stock
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
                        $inventoryStock->quantity = $inventoryStock->quantity + abs($item->getOriginal('return_quantity') - $item->return_quantity);
                        $inventoryStock->quantity_out = abs($inventoryStock->quantity_in - $inventoryStock->quantity);
                        $inventoryStock->stock_value = $inventoryStock->quantity * $inventoryStock->unit_cost;
                        $inventoryStock->save();
                    } else {
                        //throw exception
                    }
                } else {
                    //reduce stock
                    $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $saleReturn->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->where('quantity', '>', 0);
                    if ($accountingMethod === 'FIFO (First In First Out)' || empty($accountingMethod)) {
                        $inventoryStock->orderBy('created_at');
                    }
                    if ($accountingMethod === 'LIFO (Last In First Out)') {
                        $inventoryStock->orderBy('created_at', 'desc');
                    }
                    $inventoryStock = $inventoryStock->get();
                    $saleQuantity = $item->return_quantity - $item->getOriginal('return_quantity');
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
                    if (!empty($inventoryStock)) {
                        $inventoryStock->quantity = $inventoryStock->quantity + abs($item->getOriginal('return_quantity') - $item->return_quantity);
                        $inventoryStock->quantity_out = abs($inventoryStock->quantity_in - $inventoryStock->quantity);
                        $inventoryStock->stock_value = $inventoryStock->quantity * $inventoryStock->unit_cost;
                        $inventoryStock->save();
                    } else {
                        //throw exception
                    }
                }
            }
        });
    }
}
