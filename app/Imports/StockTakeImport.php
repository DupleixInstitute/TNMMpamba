<?php

namespace App\Imports;

use App\Models\InventoryStockTake;
use App\Models\InventoryStockTakeItem;
use App\Models\Setting;
use App\Notifications\InventoryStockTakeExpectedQuantityModificationAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;

class StockTakeImport implements ToModel, ShouldQueue, WithChunkReading, WithHeadingRow, WithEvents
{
    public InventoryStockTake $stockTake;

    public function __construct(InventoryStockTake $stockTake)
    {
        $this->stockTake = $stockTake;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        $stockTakeItem = InventoryStockTakeItem::where('inventory_stock_take_id', $this->stockTake->id)
            ->where('variation_sku', $row['sku'])
            ->first();
        if (!empty($stockTakeItem)) {
            if ($this->stockTake->quantity_expected != $row['expected']) {
                //tempering with expected quantity
                Notification::route('mail', Setting::where('setting_key', 'company_email')->first()->setting_value)->notify(new InventoryStockTakeExpectedQuantityModificationAlert($this->stockTake, $row['counted'], $this->stockTake->quantity_expected));
            }
            $stockTakeItem->quantity_counted = $row['counted'];
            $stockTakeItem->save();
        } else {
            //alert
        }
        return $stockTakeItem;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function registerEvents(): array
    {
        return [
            // Array callable, refering to a static method.
            AfterImport::class => [self::class, 'AfterImport'],

        ];
    }

    public static function afterImport(AfterImport $event)
    {
        $importer = $event->getConcernable();
        $stockTake = $importer->stockTake;
        $stockTake->status = 'completed';
        $stockTake->save();
        //raise stock take completed alert
    }
}
