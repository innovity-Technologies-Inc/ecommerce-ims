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
                    'type' => 'Product'
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
                    'type' => 'Variant'
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
        float $unitCost = 0,
        float $cost = 0
    ): void {
        StockLedger::create([
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'warehouse_id' => $warehouseId,
            'batch_id' => $batchId,
            'supplier_id' => $supplierId,
            'change_qty' => $changeQty,
            'unit_cost' => $unitCost, // Always the purchase unit cost
            'cost' => $cost,           // The transaction value (qty * price)
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

        $query = $flexSearch->apply($query, [], $searchTerm, $searchableColumns);

        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest': $query->oldest(); break;
            case 'a-z': $query->orderBy('name', 'asc'); break;
            case 'z-a': $query->orderBy('name', 'desc'); break;
            case 'latest':
            default: $query->latest(); break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Get Stock Report (Inventory Levels).
     */
    public function getStockReport(array $params = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = InventoryLevel::with(['product', 'variant', 'warehouse', 'batch']);

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;

        // Custom search for products/variants within inventory levels
        if ($searchTerm) {
            $query->whereHas('product', function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            })->orWhereHas('variant', function ($q) use ($searchTerm) {
                $q->where('variant_name', 'like', "%{$searchTerm}%");
            });
        }

        if (isset($params['warehouse_id']) && $params['warehouse_id'] !== 'all') {
            $query->where('warehouse_id', $params['warehouse_id']);
        }

        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest': $query->oldest(); break;
            case 'stock_low': $query->orderBy('current_quantity', 'asc'); break;
            case 'stock_high': $query->orderBy('current_quantity', 'desc'); break;
            case 'latest':
            default: $query->latest(); break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Get Batch Report.
     */
    public function getBatchReport(array $params = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Batch::with(['purchaseOrder', 'warehouse', 'items', 'supplier']);

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['batch_number'];

        $query = $flexSearch->apply($query, [], $searchTerm, $searchableColumns);

        if (isset($params['warehouse_id']) && $params['warehouse_id'] !== 'all') {
            $query->where('warehouse_id', $params['warehouse_id']);
        }

        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest': $query->oldest(); break;
            case 'latest':
            default: $query->latest(); break;
        }

        return $query->paginate($perPage);
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
     * Delete the specified warehouse.
     */
    public function deleteWarehouse(Warehouse $warehouse): void
    {
        $warehouse->delete();
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
            case 'oldest': $query->oldest(); break;
            case 'a-z': $query->orderBy('name', 'asc'); break;
            case 'z-a': $query->orderBy('name', 'desc'); break;
            case 'latest':
            default: $query->latest(); break;
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
}
