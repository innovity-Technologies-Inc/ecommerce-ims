<?php

namespace App\Services;

use App\HelperClass;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminProfileService
{
    /**
     * Update basic profile details.
     */
    public function updateDetails(int $id, array $data): bool
    {
        $admin = Admin::findOrFail($id);

        return $admin->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /**
     * Update profile password.
     */
    public function updatePassword(int $id, string $password): bool
    {
        $admin = Admin::findOrFail($id);

        return $admin->update([
            'password' => Hash::make($password),
        ]);
    }

    /**
     * Update profile avatar.
     */
    public function updateAvatar(int $id, $avatarFile): bool
    {
        $admin = Admin::findOrFail($id);

        // Delete old avatar if exists
        if ($admin->avatar) {
            HelperClass::file_delete($admin->avatar);
        }

        if ($admin->image) {
             HelperClass::file_delete($admin->image);
        }

        $path = HelperClass::file_upload($avatarFile, 'admins');

        // Sync both fields to be safe and ensure they are saved
        return $admin->update([
            'avatar' => $path,
            'image' => $path,
        ]);
    }}
