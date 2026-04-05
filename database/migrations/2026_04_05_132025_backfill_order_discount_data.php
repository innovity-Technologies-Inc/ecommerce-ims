<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Order::with('orderItems')->chunk(100, function ($orders) {
            foreach ($orders as $order) {
                $orderProductDiscountTotal = 0;
                $couponDiscountTotal = (float) $order->discount;
                $orderSubtotal = (float) $order->subtotal;

                foreach ($order->orderItems as $item) {
                    // 1. Resolve Regular Price
                    $regularPrice = 0;
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::find($item->product_variant_id);
                        $regularPrice = $variant ? (float) $variant->regular_price : (float) $item->unit_price;
                    } else {
                        $product = Product::find($item->product_id);
                        $regularPrice = $product ? (float) $product->regular_price : (float) $item->unit_price;
                    }

                    // 2. Calculate Product Discount (Difference between Regular and what was the Unit Price)
                    // Note: Existing unit_price was the price AFTER product discount but BEFORE coupon.
                    $sellingPriceBeforeCoupon = (float) $item->unit_price;
                    $productDiscountPerUnit = max(0, $regularPrice - $sellingPriceBeforeCoupon);
                    $orderProductDiscountTotal += ($productDiscountPerUnit * $item->quantity);

                    // 3. Calculate Coupon Discount (Proportional share of total order discount)
                    $itemCouponDiscount = 0;
                    if ($couponDiscountTotal > 0 && $orderSubtotal > 0) {
                        $itemSubtotalBeforeCoupon = $sellingPriceBeforeCoupon * $item->quantity;
                        $itemCouponDiscount = ($itemSubtotalBeforeCoupon / $orderSubtotal) * $couponDiscountTotal;
                    }

                    // 4. Update Order Item
                    $finalUnitPrice = $sellingPriceBeforeCoupon - ($itemCouponDiscount / $item->quantity);
                    $item->update([
                        'regular_price' => $regularPrice,
                        'product_discount' => $productDiscountPerUnit,
                        'coupon_discount' => ($itemCouponDiscount / $item->quantity),
                        'unit_price' => $finalUnitPrice,
                        'total_price' => $finalUnitPrice * $item->quantity
                    ]);
                }

                // 5. Update Order Header
                $order->update([
                    'product_discount' => $orderProductDiscountTotal
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this is complex as it modifies core financial data. 
        // We typically don't revert data backfills once they are confirmed.
    }
};
