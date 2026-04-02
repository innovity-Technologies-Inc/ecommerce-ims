<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\InventoryLevel;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockLedger;
use App\Models\Supplier;
use App\Models\Warehouse;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryService
{
    /**
     * Get all received products that are not yet allocated to a warehouse.
     * Calculated as: Global Stock - Sum(Warehouse Inventories)
     */
    public function getUnallocatedStock(array $params = [], int $perPage = 10): Collection
    {
        $unallocated = collect();

        // Simple Products
        $products = Product::where('stock', '>', 0)->get();
        foreach ($products as $product) {
            $allocatedQty = InventoryLevel::where('product_id', $product->id)
                ->whereNull('product_variant_id')
                ->sum('current_quantity');

            $diff = $product->stock - $allocatedQty;

            if ($diff > 0) {
                $unallocated->push([
                    'id' => $product->id,
                    'variant_id' => null,
                    'name' => $product->name,
                    'variant_name' => 'N/A',
                    'sku' => 'N/A',
                    'stock' => $diff,
                    'type' => 'Product',
                ]);
            }
        }

        // Product Variants
        $variants = ProductVariant::with('product')->where('stock', '>', 0)->get();
        foreach ($variants as $variant) {
            $allocatedQty = InventoryLevel::where('product_variant_id', $variant->id)
                ->sum('current_quantity');

            $diff = $variant->stock - $allocatedQty;

            if ($diff > 0) {
                $unallocated->push([
                    'id' => $variant->product_id,
                    'variant_id' => $variant->id,
                    'name' => $variant->product->name,
                    'variant_name' => $variant->variant_name,
                    'sku' => $variant->sku,
                    'stock' => $diff,
                    'type' => 'Variant',
                ]);
            }
        }

        return $unallocated;
    }

    /**
     * Allocate stock to a warehouse.
     */
    public function allocateStock(array $data): void
    {
        DB::transaction(function () use ($data) {
            $productId = $data['product_id'];
            $variantId = $data['product_variant_id'] ?? null;
            $warehouseId = $data['warehouse_id'];
            $quantity = (int) $data['quantity'];

            if ($variantId) {
                $variant = ProductVariant::findOrFail($variantId);
                // Check if sufficient unallocated stock exists
                $allocatedQty = InventoryLevel::where('product_variant_id', $variantId)->sum('current_quantity');
                $unallocated = $variant->stock - $allocatedQty;

                if ($unallocated < $quantity) {
                    throw new \Exception("Insufficient unallocated stock for variant {$variant->variant_name}. Available: {$unallocated}");
                }
                // No decrement of variant->stock here because it represents the Total System Stock.
            } else {
                $product = Product::findOrFail($productId);
                $allocatedQty = InventoryLevel::where('product_id', $productId)->whereNull('product_variant_id')->sum('current_quantity');
                $unallocated = $product->stock - $allocatedQty;

                if ($unallocated < $quantity) {
                    throw new \Exception("Insufficient unallocated stock for product {$product->name}. Available: {$unallocated}");
                }
                // No decrement of product->stock here.
            }

            // Update or create inventory level in warehouse
            $inventoryLevel = InventoryLevel::firstOrNew([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'batch_id' => null, // Manual allocations usually don't have a specific PO batch
            ]);

            $inventoryLevel->current_quantity += $quantity;
            $inventoryLevel->save();

            // Log Allocation (Internal Movement)
            $this->logStockChange(
                $productId,
                $variantId,
                $warehouseId,
                $quantity,
                'ALLOCATION',
                'MOVE_TO_WAREHOUSE',
                'MANUAL'
            );
        });
    }

    /**
     * Centralized method to log stock ledger changes.
     */
    public function logStockChange(
        int $productId,
        ?int $variantId,
        ?int $warehouseId,
        int $changeQty,
        string $transactionType,
        ?string $reasonCode = null,
        ?string $referenceId = null,
        ?int $batchId = null,
        ?int $supplierId = null,
        ?int $batchSerialId = null
    ): void {
        StockLedger::create([
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'warehouse_id' => $warehouseId,
            'batch_id' => $batchId,
            'batch_serial_id' => $batchSerialId,
            'supplier_id' => $supplierId,
            'change_qty' => $changeQty,
            'transaction_type' => $transactionType,
            'reason_code' => $reasonCode,
            'reference_id' => $referenceId,
        ]);
    }

    /**
     * Get all warehouses with search and sorting.
     */
    public function getAllWarehouses(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Warehouse::query();

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['name', 'location'];

        $filters = [];
        if (isset($params['is_quarantine']) && $params['is_quarantine'] !== 'all') {
            $filters['is_quarantine'] = $params['is_quarantine'];
        }

        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest': $query->oldest();
                break;
            case 'a-z': $query->orderBy('name', 'asc');
                break;
            case 'z-a': $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default: $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Get Stock Report (Inventory Levels).
     */
    public function getStockReport(array $params = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = InventoryLevel::with(['product', 'variant', 'warehouse', 'batch']);

        $searchTerm = $params['search'] ?? null;

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('product', function ($pq) use ($searchTerm) {
                    $pq->where('name', 'like', "%{$searchTerm}%");
                })->orWhereHas('variant', function ($vq) use ($searchTerm) {
                    $vq->where('variant_name', 'like', "%{$searchTerm}%");
                })->orWhereHas('batch', function ($bq) use ($searchTerm) {
                    $bq->where('batch_number', 'like', "%{$searchTerm}%");
                });
            });
        }

        if (isset($params['warehouse_id']) && $params['warehouse_id'] !== 'all') {
            $query->where('warehouse_id', $params['warehouse_id']);
        }

        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest': $query->oldest();
                break;
            case 'stock_low': $query->orderBy('current_quantity', 'asc');
                break;
            case 'stock_high': $query->orderBy('current_quantity', 'desc');
                break;
            case 'latest':
            default: $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Get Damaged/Quarantine Products Report.
     */
    public function getDamagedReport(array $params = [], int $perPage = 15): LengthAwarePaginator
    {
        // Focus on items where damaged_quantity > 0
        $query = InventoryLevel::with(['product', 'variant', 'warehouse', 'batch'])
            ->where('damaged_quantity', '>', 0);

        $searchTerm = $params['search'] ?? null;

        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('product', function ($pq) use ($searchTerm) {
                    $pq->where('name', 'like', "%{$searchTerm}%");
                })->orWhereHas('variant', function ($vq) use ($searchTerm) {
                    $vq->where('variant_name', 'like', "%{$searchTerm}%");
                })->orWhereHas('batch', function ($bq) use ($searchTerm) {
                    $bq->where('batch_number', 'like', "%{$searchTerm}%");
                });
            });
        }

        $sort = $params['sort'] ?? 'batch_number';
        switch ($sort) {
            case 'oldest': $query->oldest();
                break;
            case 'stock_low': $query->orderBy('damaged_quantity', 'asc');
                break;
            case 'stock_high': $query->orderBy('damaged_quantity', 'desc');
                break;
            case 'batch_number':
                $query->join('batches', 'inventory_levels.batch_id', '=', 'batches.id')
                    ->orderBy('batches.batch_number', 'asc')
                    ->select('inventory_levels.*');
                break;
            case 'latest':
            default: $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Get Batch Report.
     */
    public function getBatchReport(array $params = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Batch::with(['purchaseOrder', 'warehouse', 'supplier']);

        $searchTerm = $params['search'] ?? null;
        if ($searchTerm) {
            $query->where('batch_number', 'like', "%{$searchTerm}%");
        }

        if (isset($params['warehouse_id']) && $params['warehouse_id'] !== 'all') {
            $query->where('warehouse_id', $params['warehouse_id']);
        }

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
     * Get details for a specific inventory level record.
     */
    public function getProductStockDetails(int $inventoryLevelId): InventoryLevel
    {
        return InventoryLevel::with(['product', 'variant', 'warehouse', 'batch.supplier', 'batch.purchaseOrder'])->findOrFail($inventoryLevelId);
    }

    /**
     * Delete the specified warehouse.
     */
    public function deleteWarehouse(Warehouse $warehouse): void
    {
        $warehouse->delete();
    }

    /**
     * Store a newly created warehouse.
     */
    public function storeWarehouse(array $data): Warehouse
    {
        return Warehouse::create([
            'name' => $data['name'],
            'location' => $data['location'],
            'is_quarantine' => $data['is_quarantine'] ?? false,
        ]);
    }

    /**
     * Update the specified warehouse.
     */
    public function updateWarehouse(Warehouse $warehouse, array $data): Warehouse
    {
        $warehouse->update([
            'name' => $data['name'],
            'location' => $data['location'],
            'is_quarantine' => $data['is_quarantine'] ?? false,
        ]);

        return $warehouse;
    }

    /**
     * Get all suppliers with search and sorting.
     */
    public function getAllSuppliers(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Supplier::query();

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['name', 'email', 'mobile', 'address'];

        $query = $flexSearch->apply($query, [], $searchTerm, $searchableColumns);

        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest': $query->oldest();
                break;
            case 'a-z': $query->orderBy('name', 'asc');
                break;
            case 'z-a': $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default: $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Store a newly created supplier.
     */
    public function storeSupplier(array $data): Supplier
    {
        return Supplier::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'address' => $data['address'],
        ]);
    }

    /**
     * Update the specified supplier.
     */
    public function updateSupplier(Supplier $supplier, array $data): Supplier
    {
        $supplier->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'address' => $data['address'],
        ]);

        return $supplier;
    }

    /**
     * Delete the specified supplier.
     */
    public function deleteSupplier(Supplier $supplier): void
    {
        $supplier->delete();
    }

    /**
     * Get a specific supplier with its paginated purchase orders.
     */
    public function getSupplierWithOrders(int $id, int $perPage = 10): array
    {
        $supplier = Supplier::findOrFail($id);
        $purchaseOrders = $supplier->purchaseOrders()->latest()->paginate($perPage);

        return [
            'supplier' => $supplier,
            'purchase_orders' => $purchaseOrders,
        ];
    }
}
