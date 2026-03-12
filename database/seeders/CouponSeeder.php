<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'apply_for' => 'total_product_price',
                'min_spend' => 100.00,
                'discount_type' => 'percentage',
                'discount_amount' => 10.00,
                'max_discount_amount' => 50.00,
                'usage_limit' => 100,
                'active_on' => now()->subDays(1),
                'expired_on' => now()->addMonths(1),
                'status' => true,
            ],
            [
                'code' => 'FREESHIPPING',
                'apply_for' => 'shipping_cost',
                'min_spend' => 500.00,
                'discount_type' => 'percentage',
                'discount_amount' => 100.00,
                'max_discount_amount' => 50.00,
                'usage_limit' => 50,
                'active_on' => now()->subDays(1),
                'expired_on' => now()->addMonths(1),
                'status' => true,
            ],
            [
                'code' => 'FLAT50',
                'apply_for' => 'total_product_price',
                'min_spend' => 200.00,
                'discount_type' => 'fixed',
                'discount_amount' => 50.00,
                'max_discount_amount' => null,
                'usage_limit' => null,
                'active_on' => now()->subDays(1),
                'expired_on' => now()->addMonths(1),
                'status' => true,
            ],
            [
                'code' => 'EXPIRED20',
                'apply_for' => 'total_product_price',
                'min_spend' => 0.00,
                'discount_type' => 'percentage',
                'discount_amount' => 20.00,
                'max_discount_amount' => 100.00,
                'usage_limit' => 10,
                'active_on' => now()->subMonths(2),
                'expired_on' => now()->subDays(1),
                'status' => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::create($coupon);
        }
    }
}
