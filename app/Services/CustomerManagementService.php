<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerManagementService
{
    /**
     * Get all registered customers.
     */
    public function getAllCustomers(): LengthAwarePaginator
    {
        return User::latest()->paginate(10);
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
