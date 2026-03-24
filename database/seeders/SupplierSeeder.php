<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::create([
            'name' => 'Tech Supplier Inc.',
            'email' => 'contact@techsupplier.com',
            'mobile' => '1234567890',
            'address' => '123 Tech Avenue',
        ]);
        Supplier::create([
            'name' => 'Fashion Source',
            'email' => 'info@fashionsource.com',
            'mobile' => '9876543210',
            'address' => '456 Style Street',
        ]);
    }
}
