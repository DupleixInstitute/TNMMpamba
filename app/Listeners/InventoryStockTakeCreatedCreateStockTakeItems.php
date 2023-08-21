<?php

namespace App\Listeners;

use App\Events\InventoryStockTakeCreated;
use App\Models\InventoryProductVariation;
use App\Models\InventoryStockTakeItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class InventoryStockTakeCreatedCreateStockTakeItems implements ShouldQueue
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
     * @param \App\Events\InventoryStockTakeCreated $event
     * @return void
     */
    public function handle(InventoryStockTakeCreated $event)
    {
        $stockTake = $event->stockTake;
        $brands = $stockTake->brands;
        $categories = $stockTake->categories;
        $products = InventoryProductVariation::leftJoin('inventory_products', 'inventory_products.id', 'inventory_product_variations.inventory_product_id')
            ->when(count($brands), function ($query) use ($brands) {
                $query->whereIn('inventory_products.inventory_product_brand_id', $brands);
            })
            ->when(count($categories), function ($query) use ($categories) {
                $query->whereIn('inventory_products.inventory_product_category_id', $categories);
            })
            ->select([
                'inventory_product_variations.id as variation_id',
                'inventory_product_variations.name as variation_name',
                'inventory_product_variations.value as variation_value',
                'inventory_product_variations.sku as variation_sku',
                'inventory_product_variations.purchase_price as variation_purchase_price',
                'inventory_product_variations.default_selling_price as variation_default_selling_price',
                'inventory_products.id as id',
                'inventory_products.name as name',
                'inventory_products.sku as sku',
                'inventory_products.not_for_selling',
                'inventory_products.product_type',
                'inventory_products.selling_price_tax_type',
                'inventory_products.purchase_price',
                'inventory_products.default_selling_price',
                'inventory_products.delivery_option',
                'inventory_products.active',
            ])
            ->selectSub(function ($query) use ($stockTake) {
                $query->selectRaw('coalesce(sum(quantity),0)')
                    ->from('inventory_product_stock')
                    ->whereColumn('inventory_product_stock.inventory_product_variation_id', 'inventory_product_variations.id')
                    ->where('inventory_product_stock.inventory_warehouse_id', $stockTake->inventory_warehouse_id)
                    ->limit(1);
            }, 'quantity')
            ->having('quantity', '>', 0)
            ->get();
        foreach ($products as $product) {
            $stockTakeItem = new InventoryStockTakeItem();
            $stockTakeItem->inventory_stock_take_id = $stockTake->id;
            $stockTakeItem->inventory_product_id = $product->id;
            $stockTakeItem->inventory_product_variation_id = $product->variation_id;
            $stockTakeItem->quantity_expected = $product->quantity;
            $stockTakeItem->product_name = $product->name;
            $stockTakeItem->variation_name = $product->variation_name;
            $stockTakeItem->variation_sku = $product->variation_sku;
            $stockTakeItem->product_sku = $product->sku;
            $stockTakeItem->save();
        }
        $stockTake->variance = $stockTake->items->sum('quantity_expected') - $stockTake->items->sum('quantity_counted');
        $stockTake->items_created = 1;
        $stockTake->save();
    }

}
