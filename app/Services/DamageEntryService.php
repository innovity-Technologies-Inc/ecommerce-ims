<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\BatchProduct;
use App\Models\BatchSerial;
use App\Models\InventoryLevel;
use App\Models\Wastage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DamageEntryService
{
    public function __construct(protected InventoryService $inventoryService) {}

    /**
     * Store a new warehouse damage entry.
     */
    public function storeDamage(array $data): Wastage
    {
        return DB::transaction(function () use ($data) {
            $qty = (int) $data['quantity'];
            $batchId = $data['batch_id'];
            $warehouseId = $data['warehouse_id'];
            $productId = $data['product_id'];
            $variantId = $data['product_variant_id'] ?? null;

            // 1. Create Wastage record
            $wastage = Wastage::create([
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'warehouse_id' => $warehouseId,
                'batch_id' => $batchId,
                'quantity' => $qty,
                'reason' => $data['reason'] ?? 'Warehouse Damage',
                'created_by' => Auth::guard('admin')->id(),
            ]);

            // 2. Handle Serials if provided
            if (! empty($data['serial_ids'])) {
                BatchSerial::whereIn('id', $data['serial_ids'])->update([
                    'product_status' => 'damaged',
                    'stock_status' => 'wastage',
                ]);
            }

            // 3. Update InventoryLevel: Decrement current, Increment damaged
            $inventory = InventoryLevel::where([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'batch_id' => $batchId,
            ])->first();

            if ($inventory) {
                $inventory->decrement('current_quantity', $qty);
                $inventory->increment('damaged_quantity', $qty);
            }

            // 4. Update Batch and BatchProduct
            $batch = Batch::find($batchId);
            if ($batch) {
                $batch->decrement('total_saleable_qty', $qty);
                $batch->increment('total_damaged_qty', $qty);
            }

            $bp = BatchProduct::where([
                'batch_id' => $batchId,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
            ])->first();

            if ($bp) {
                $bp->decrement('saleable_qty', $qty);
                $bp->increment('damaged_qty', $qty);
            }

            // 5. Log to StockLedger (Aggregate entry)
            $this->inventoryService->logStockChange(
                productId: $productId,
                variantId: $variantId,
                warehouseId: $warehouseId,
                changeQty: -$qty,
                transactionType: 'warehouse_damage',
                reasonCode: 'Warehouse Damage',
                referenceId: 'WASTAGE-'.$wastage->id,
                batchId: $batchId,
                supplierId: null
            );

            return $wastage;
        });
    }
}
