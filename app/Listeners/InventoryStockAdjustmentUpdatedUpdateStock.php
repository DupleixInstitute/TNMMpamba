<?php

namespace App\Listeners;

use App\Events\InventoryStockAdjustmentUpdated;
use App\Models\InventoryProductStock;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class InventoryStockAdjustmentUpdatedUpdateStock  implements ShouldQueue
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
     * @param \App\Events\InventoryStockAdjustmentUpdated $event
     * @return void
     */
    public function handle(InventoryStockAdjustmentUpdated $event)
    {
        $adjustment = $event->adjustment;
        $accountingMethod = Setting::where('setting_key', 'inventory_stock_accounting_method')->first()->setting_value;
        $oldItems = clone $adjustment->items;
        $existing = $adjustment->items->pluck('id');
        $adjustment->items->each(function ($item) use ($adjustment, $accountingMethod) {
            if ($item->wasChanged('quantity')) {
                if($item->stock_adjustment_type==='subtraction'){
                    if ($item->getOriginal('quantity') > $item->quantity) {
                        //increase stock
                        if ($accountingMethod === 'FIFO (First In First Out)' || empty($accountingMethod)) {
                            $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $adjustment->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->where('quantity', '>', 0)->orderBy('created_at')->first();
                            if (empty($inventoryStock)) {
                                //just find the last stock record
                                $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $adjustment->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->orderBy('created_at')->first();
                            }
                        } elseif ($accountingMethod === 'LIFO (Last In First Out)') {
                            $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $adjustment->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->where('quantity', '>', 0)->orderBy('created_at', 'desc')->first();
                            if (empty($inventoryStock)) {
                                //just find the last stock record
                                $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $adjustment->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->orderBy('created_at', 'desc')->first();
                            }
                        } else {
                            $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $adjustment->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->orderBy('created_at', 'desc')->first();
                        }
                        if (!empty($inventoryStock)) {
                            $inventoryStock->quantity = $inventoryStock->quantity + abs($item->getOriginal('quantity') - $item->quantity);
                            $inventoryStock->quantity_out = abs($inventoryStock->quantity_in - $inventoryStock->quantity);
                            $inventoryStock->stock_value = $inventoryStock->quantity * $inventoryStock->unit_cost;
                            $inventoryStock->save();
                        } else {
                            //throw exception
                        }
                    } else {
                        //reduce stock
                        $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $adjustment->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->where('quantity', '>', 0);
                        if ($accountingMethod === 'FIFO (First In First Out)' || empty($accountingMethod)) {
                            $inventoryStock->orderBy('created_at');
                        }
                        if ($accountingMethod === 'LIFO (Last In First Out)') {
                            $inventoryStock->orderBy('created_at', 'desc');
                        }
                        $inventoryStock = $inventoryStock->get();
                        $adjustmentQuantity = $item->quantity - $item->getOriginal('quantity');
                        foreach ($inventoryStock as $value) {
                            if ($value->quantity >= $adjustmentQuantity) {
                                $value->quantity = $value->quantity - $adjustmentQuantity;
                                $adjustmentQuantity = 0;
                            } else {
                                $adjustmentQuantity = $adjustmentQuantity - $value->quantity;
                                $value->quantity = 0;
                            }
                            $value->quantity_out = abs($value->quantity_in - $value->quantity);
                            $value->stock_value = $value->quantity * $value->unit_cost;
                            $value->save();
                            if ($adjustmentQuantity === 0) {
                                break;
                            }
                        }
                    }
                }else{
                    if ($item->getOriginal('quantity') > $item->quantity) {
                        //reduce stock
                        $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $adjustment->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->where('quantity', '>', 0);
                        if ($accountingMethod === 'FIFO (First In First Out)' || empty($accountingMethod)) {
                            $inventoryStock->orderBy('created_at');
                        }
                        if ($accountingMethod === 'LIFO (Last In First Out)') {
                            $inventoryStock->orderBy('created_at', 'desc');
                        }
                        $inventoryStock = $inventoryStock->get();
                        $adjustmentQuantity = $item->quantity - $item->getOriginal('quantity');
                        foreach ($inventoryStock as $value) {
                            if ($value->quantity >= $adjustmentQuantity) {
                                $value->quantity = $value->quantity - $adjustmentQuantity;
                                $adjustmentQuantity = 0;
                            } else {
                                $adjustmentQuantity = $adjustmentQuantity - $value->quantity;
                                $value->quantity = 0;
                            }
                            $value->quantity_out = abs($value->quantity_in - $value->quantity);
                            $value->stock_value = $value->quantity * $value->unit_cost;
                            $value->save();
                            if ($adjustmentQuantity === 0) {
                                break;
                            }
                        }
                    } else {
                        //increase stock
                        if ($accountingMethod === 'FIFO (First In First Out)' || empty($accountingMethod)) {
                            $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $adjustment->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->where('quantity', '>', 0)->orderBy('created_at')->first();
                            if (empty($inventoryStock)) {
                                //just find the last stock record
                                $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $adjustment->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->orderBy('created_at')->first();
                            }
                        } elseif ($accountingMethod === 'LIFO (Last In First Out)') {
                            $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $adjustment->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->where('quantity', '>', 0)->orderBy('created_at', 'desc')->first();
                            if (empty($inventoryStock)) {
                                //just find the last stock record
                                $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $adjustment->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->orderBy('created_at', 'desc')->first();
                            }
                        } else {
                            $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $adjustment->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->orderBy('created_at', 'desc')->first();
                        }
                        if (!empty($inventoryStock)) {
                            $inventoryStock->quantity = $inventoryStock->quantity + abs($item->getOriginal('quantity') - $item->quantity);
                            $inventoryStock->quantity_out = abs($inventoryStock->quantity_in - $inventoryStock->quantity);
                            $inventoryStock->stock_value = $inventoryStock->quantity * $inventoryStock->unit_cost;
                            $inventoryStock->save();
                        } else {
                            //throw exception
                        }
                    }
                }
            }
        });
        //decrease stock for variation item
        $adjustment->refresh();
        $adjustment->items->whereNotIn('id', $existing)->each(function ($item) use ($adjustment, $accountingMethod) {
            if ($item->stock_adjustment_type === 'addition') {
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
            } else {
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
            }
        });

        $adjustment->refresh();
        //find those deleted
        $oldItems->each(function ($item) use ($adjustment, $accountingMethod) {
            if (empty($adjustment->items->firstWhere('id', $item->id))) {
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
                }else{
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
            }

        });
    }
}
