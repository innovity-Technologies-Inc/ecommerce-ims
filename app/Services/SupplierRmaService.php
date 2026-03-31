<?php

namespace App\Services;

use App\Mail\SupplierRmaMail;
use App\Models\Batch;
use App\Models\BatchProduct;
use App\Models\BatchSerial;
use App\Models\RmaItem;
use App\Models\SupplierRma;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SupplierRmaService
{
    public function __construct(protected InventoryService $inventoryService) {}

    /**
     * Get all Supplier RMAs with search and filters.
     */
    public function getAllRmas(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = SupplierRma::with(['supplier', 'purchaseOrder']);

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['rma_number', 'remarks'];

        $filters = [];
        if (isset($params['status']) && $params['status'] !== 'all') {
            $filters['status'] = $params['status'];
        }
        if (isset($params['supplier_id']) && $params['supplier_id'] !== 'all') {
            $filters['supplier_id'] = $params['supplier_id'];
        }

        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest': $query->oldest();
                break;
            case 'latest':
            default: $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Store a new Supplier RMA.
     */
    public function storeRma(array $data): SupplierRma
    {
        return DB::transaction(function () use ($data) {
            $rma = SupplierRma::create([
                'rma_number' => $this->generateRmaNumber(),
                'supplier_id' => $data['supplier_id'],
                'purchase_order_id' => $data['purchase_order_id'] ?? null,
                'status' => 'pending',
                'notify_supplier' => $data['notify_supplier'] ?? false,
                'remarks' => $data['remarks'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                if ((int) $item['quantity'] <= 0) {
                    continue;
                }

                if (! empty($item['serial_ids'])) {
                    foreach ($item['serial_ids'] as $serialId) {
                        RmaItem::create([
                            'supplier_rma_id' => $rma->id,
                            'batch_id' => $item['batch_id'],
                            'product_id' => $item['product_id'],
                            'product_variant_id' => $item['product_variant_id'] ?? null,
                            'batch_serial_id' => $serialId,
                            'quantity' => 1,
                        ]);
                    }
                } else {
                    RmaItem::create([
                        'supplier_rma_id' => $rma->id,
                        'batch_id' => $item['batch_id'],
                        'product_id' => $item['product_id'],
                        'product_variant_id' => $item['product_variant_id'] ?? null,
                        'batch_serial_id' => null,
                        'quantity' => $item['quantity'] ?? 1,
                    ]);
                }
            }

            if ($rma->notify_supplier) {
                try {
                    Mail::to($rma->supplier->email)->send(new SupplierRmaMail($rma));
                } catch (\Exception $e) {
                    Log::error('Supplier RMA Email Error: '.$e->getMessage());
                }
            }

            return $rma;
        });
    }

    /**
     * Update RMA status.
     */
    public function updateStatus(SupplierRma $rma, string $status): void
    {
        DB::transaction(function () use ($rma, $status) {
            $rma->update(['status' => $status]);

            if ($status === 'closed') {
                $this->processClosing($rma);
            }
        });
    }

    /**
     * Process stock updates and ledger entries when RMA is closed.
     */
    protected function processClosing(SupplierRma $rma): void
    {
        foreach ($rma->rmaItems as $item) {
            $batch = $item->batch;

            // 1. Update Batch total damaged quantity
            $batch->decrement('total_damaged_qty', $item->quantity);

            // 2. Update BatchProduct damaged quantity
            BatchProduct::where([
                'batch_id' => $item->batch_id,
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
            ])->decrement('damaged_qty', $item->quantity);

            // 3. Update InventoryLevel damaged quantity
            \App\Models\InventoryLevel::where([
                'batch_id' => $item->batch_id,
                'warehouse_id' => $batch->warehouse_id,
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
            ])->decrement('damaged_quantity', $item->quantity);

            // 4. Update BatchSerials
            if ($item->batch_serial_id) {
                BatchSerial::where('id', $item->batch_serial_id)->update([
                    'product_status' => 'damaged_return',
                    'stock_status' => 'returned',
                ]);
            } else {
                // If no specific serial, update available damaged serials for this product in this batch
                BatchSerial::where([
                    'batch_id' => $item->batch_id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_status' => 'damaged',
                ])->limit($item->quantity)->update([
                    'product_status' => 'damaged_return',
                    'stock_status' => 'returned',
                ]);
            }

            // 5. Log to StockLedger
            $this->inventoryService->logStockChange(
                $item->product_id,
                $item->product_variant_id,
                $batch->warehouse_id,
                -$item->quantity,
                'RTV_Dispatch',
                'Supplier RMA',
                $rma->rma_number,
                $item->batch_id,
                $rma->supplier_id,
                $item->product->unit_cost ?? 0,
                -($item->quantity * ($item->product->unit_cost ?? 0))
            );
        }
    }

    /**
     * Generate unique RMA number.
     */
    protected function generateRmaNumber(): string
    {
        $date = now()->format('Ymd');
        $count = SupplierRma::whereDate('created_at', now()->toDateString())->count() + 1;

        return 'SRMA-'.$date.'-'.str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
