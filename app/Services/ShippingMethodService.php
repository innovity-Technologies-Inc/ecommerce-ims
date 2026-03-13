<?php

namespace App\Services;

use App\Models\ShippingMethod;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ShippingMethodService
{
    /**
     * Get paginated shipping methods with search and sorting.
     */
    public function getPaginatedMethods(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = ShippingMethod::query();

        $filters = [];
        if (isset($params['status']) && $params['status'] !== '') {
            $filters['status'] = $params['status'];
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['name', 'short_description'];

        // Apply Search and Filtering using FlexSearch
        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

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
     * Get all active shipping methods.
     */
    public function getActiveMethods(): Collection
    {
        return ShippingMethod::where('status', true)->get();
    }

    /**
     * Store a new shipping method.
     */
    public function storeMethod(array $data): ShippingMethod
    {
        return ShippingMethod::create([
            'name' => $data['name'],
            'price' => $data['price'],
            'short_description' => $data['short_description'] ?? null,
            'status' => $data['status'] ?? true,
        ]);
    }

    /**
     * Update an existing shipping method.
     */
    public function updateMethod(ShippingMethod $method, array $data): ShippingMethod
    {
        $method->update([
            'name' => $data['name'],
            'price' => $data['price'],
            'short_description' => $data['short_description'] ?? null,
            'status' => $data['status'] ?? true,
        ]);

        return $method;
    }

    /**
     * Delete a shipping method.
     */
    public function deleteMethod(ShippingMethod $method): bool
    {
        return $method->delete();
    }

    /**
     * Toggle the status of a shipping method.
     */
    public function toggleStatus(ShippingMethod $method): bool
    {
        $method->status = ! $method->status;

        return $method->save();
    }
}
