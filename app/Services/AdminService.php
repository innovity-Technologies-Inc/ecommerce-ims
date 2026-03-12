<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Admin;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    /**
     * Get all admins with pagination.
     */
    public function getAllAdmins(int $perPage = 10): LengthAwarePaginator
    {
        return Admin::latest()->paginate($perPage);
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

        return Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'image' => $imagePath,
        ]);
    }

    /**
     * Find an admin by ID.
     */
    public function findAdmin(int $id): ?Admin
    {
        return Admin::find($id);
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
