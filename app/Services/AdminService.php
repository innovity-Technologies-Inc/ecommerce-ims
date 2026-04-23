<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Admin;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    /**
     * Get all admins with search and sorting.
     */
    public function getAllAdmins(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Admin::query();

        $filters = [];
        if (! empty($params['email'])) {
            $filters['email'] = $params['email'];
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['name', 'email'];

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
     * Store a newly created admin.
     */
    public function storeAdmin(array $data): Admin
    {
        $imagePath = null;
        if (isset($data['image'])) {
            $imagePath = HelperClass::file_upload($data['image'], 'admins');
        }

        $admin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'image' => $imagePath,
            'is_time_tracking' => $data['is_time_tracking'] ?? false,
            'salary_amount' => $data['salary_amount'] ?? 0,
            'daily_work_hours' => $data['daily_work_hours'] ?? 8,
        ]);

        if (isset($data['role'])) {
            $admin->assignRole($data['role']);
        }

        return $admin;
    }

    /**
     * Find an admin by ID.
     */
    public function findAdmin(int $id): ?Admin
    {
        return Admin::with('roles')->find($id);
    }

    /**
     * Update the specified admin.
     */
    public function updateAdmin(int $id, array $data): Admin
    {
        $admin = Admin::findOrFail($id);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'is_time_tracking' => $data['is_time_tracking'] ?? false,
            'salary_amount' => $data['salary_amount'] ?? 0,
            'daily_work_hours' => $data['daily_work_hours'] ?? 8,
        ];

        if (isset($data['image'])) {
            if ($admin->image) {
                HelperClass::file_delete($admin->image);
            }
            $updateData['image'] = HelperClass::file_upload($data['image'], 'admins');
        }

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $admin->update($updateData);

        if (isset($data['role'])) {
            $admin->syncRoles([$data['role']]);
        }

        return $admin;
    }

    /**
     * Delete the specified admin.
     */
    public function deleteAdmin(int $id): bool
    {
        $admin = Admin::find($id);

        if ($admin) {
            if ($admin->image) {
                HelperClass::file_delete($admin->image);
            }

            return $admin->delete();
        }

        return false;
    }
}
