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
        $suppliers = [
            ['name' => 'Apple Inc.', 'email' => 'sales@apple.com', 'mobile' => '1234567890', 'address' => 'Cupertino, CA'],
            ['name' => 'Samsung Electronics', 'email' => 'sales@samsung.com', 'mobile' => '0987654321', 'address' => 'Seoul, South Korea'],
            ['name' => 'Nike Global', 'email' => 'support@nike.com', 'mobile' => '1122334455', 'address' => 'Beaverton, OR'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                ['email' => $supplier['email']],
                [
                    'name' => $supplier['name'],
                    'mobile' => $supplier['mobile'],
                    'address' => $supplier['address'],
                ]
            );
        }
    }
}
