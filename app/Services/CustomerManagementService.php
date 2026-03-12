<?php

namespace App\Services;

use App\Models\User;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerManagementService
{
    /**
     * Get all registered customers with search and sorting.
     */
    public function getAllCustomers(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = User::query();

        // Apply Search using FlexSearch
        if (! empty($params['search'])) {
            $flexSearch = app(FlexSearch::class);
            $query = $flexSearch->apply($query, [], $params['search'], ['name', 'email', 'mobile']);
        }

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
     * Get a specific customer with their orders.
     */
    public function getCustomerWithOrders(int $id): User
    {
        return User::with(['orders' => function ($query) {
            $query->latest();
        }])->findOrFail($id);
    }

    /**
     * Toggle customer active/inactive status.
     */
    public function toggleCustomerStatus(int $id): bool
    {
        $user = User::findOrFail($id);
        $user->status = ! $user->status;

        return $user->save();
    }

    /**
     * Delete a customer account.
     */
    public function deleteCustomer(int $id): bool
    {
        $user = User::findOrFail($id);

        return $user->delete();
    }
}
