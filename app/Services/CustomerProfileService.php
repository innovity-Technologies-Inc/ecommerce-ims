<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CustomerProfileService
{
    /**
     * Update customer basic profile information.
     */
    public function updateProfile(int $userId, array $data): bool
    {
        try {
            $user = User::findOrFail($userId);

            return $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating customer profile: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Update customer password.
     */
    public function updatePassword(int $userId, string $newPassword, ?string $currentPassword = null): array
    {
        try {
            $user = User::findOrFail($userId);

            // If user has a password set, verify the current one
            if ($user->password && ! Hash::check($currentPassword, $user->password)) {
                return [
                    'status' => false,
                    'message' => 'Current password is incorrect.',
                    'error_type' => 'current_password',
                ];
            }

            $user->update([
                'password' => Hash::make($newPassword),
            ]);

            return [
                'status' => true,
                'message' => 'Password changed successfully.',
            ];
        } catch (\Exception $e) {
            Log::error('Error updating customer password: '.$e->getMessage());

            return [
                'status' => false,
                'message' => 'An error occurred while updating your password.',
            ];
        }
    }

    /**
     * Update customer address information.
     */
    public function updateAddress(int $userId, array $data): bool
    {
        try {
            $user = User::findOrFail($userId);

            return $user->update([
                'address' => $data['address'],
                'city' => $data['city'],
                'state' => $data['state'] ?? null,
                'country' => $data['country'] ?? null,
                'zip' => $data['zip'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating customer address: '.$e->getMessage());

            return false;
        }
    }
}
