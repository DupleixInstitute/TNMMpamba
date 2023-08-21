<?php

namespace App\Listeners;

use App\Events\InventoryProductSaleUpdated;
use App\Models\InventoryProductStock;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class InventoryProductSaleUpdatedUpdateStock implements ShouldQueue
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
     * @param \App\Events\InventoryProductSaleUpdated $event
     * @return void
     */
    public function handle(InventoryProductSaleUpdated $event)
    {
        $sale = $event->sale;
        $accountingMethod = Setting::where('setting_key', 'inventory_stock_accounting_method')->first()->setting_value;
        $oldItems = clone $sale->items;
        if ($sale->wasChanged('status') && $sale->getOriginal('status') === 'completed') {
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
        $existing = $sale->items->pluck('id');
        if ($sale->isClean('status') && $sale->status === 'completed') {
            $sale->items->each(function ($item) use ($sale, $accountingMethod) {
                if ($item->wasChanged('quantity')) {
                    if ($item->getOriginal('quantity') > $item->quantity) {
                        //increase stock
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
                            $inventoryStock->quantity = $inventoryStock->quantity + abs($item->getOriginal('quantity') - $item->quantity);
                            $inventoryStock->quantity_out = abs($inventoryStock->quantity_in - $inventoryStock->quantity);
                            $inventoryStock->stock_value = $inventoryStock->quantity * $inventoryStock->unit_cost;
                            $inventoryStock->save();
                        } else {
                            //throw exception
                        }
                    } else {
                        //reduce stock
                        $inventoryStock = InventoryProductStock::where('inventory_warehouse_id', $sale->inventory_warehouse_id)->where('inventory_product_id', $item->inventory_product_id)->where('inventory_product_variation_id', $item->inventory_product_variation_id)->where('quantity', '>', 0);
                        if ($accountingMethod === 'FIFO (First In First Out)' || empty($accountingMethod)) {
                            $inventoryStock->orderBy('created_at');
                        }
                        if ($accountingMethod === 'LIFO (Last In First Out)') {
                            $inventoryStock->orderBy('created_at', 'desc');
                        }
                        $inventoryStock = $inventoryStock->get();
                        $saleQuantity = $item->quantity - $item->getOriginal('quantity');
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
                }
            });
            //decrease stock for variation item
            $sale->refresh();
            $sale->items->whereNotIn('id', $existing)->each(function ($item) use ($sale, $accountingMethod) {
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
        if ($sale->isDirty('status') && $sale->status === 'completed') {
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
        $sale->refresh();
        //find those deleted
        $oldItems->each(function ($item) use ($sale, $accountingMethod) {
            if(empty( $sale->items->firstWhere('id', $item->id))){
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
            }

        });
    }
}
