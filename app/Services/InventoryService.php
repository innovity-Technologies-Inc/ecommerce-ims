<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\Warehouse;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;

class InventoryService
{
    /**
     * Get all warehouses with search and sorting.
     */
    public function getAllWarehouses(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Warehouse::query();

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['name', 'location'];

        // Apply Search and Filtering using FlexSearch
        $query = $flexSearch->apply($query, [], $searchTerm, $searchableColumns);

        // Apply Sorting
        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('name', 'asc');
                break;
            case 'z-a':
                $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
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

        // Apply Search and Filtering using FlexSearch
        $query = $flexSearch->apply($query, [], $searchTerm, $searchableColumns);

        // Apply Sorting
        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('name', 'asc');
                break;
            case 'z-a':
                $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
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
}
