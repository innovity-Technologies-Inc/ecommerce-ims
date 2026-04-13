<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;

class CouponService
{
    /**
     * Get all coupons with search and filtering.
     */
    public function getAllCoupons(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Coupon::query();

        // Specific Filtering
        if (! empty($params['apply_for'])) {
            $query->where('apply_for', $params['apply_for']);
        }

        if (isset($params['status']) && $params['status'] !== '') {
            $query->where('status', $params['status']);
        }

        // Date Range Filtering for active_on
        if (! empty($params['active_on_from'])) {
            $query->whereDate('active_on', '>=', $params['active_on_from']);
        }
        if (! empty($params['active_on_to'])) {
            $query->whereDate('active_on', '<=', $params['active_on_to']);
        }

        // Date Range Filtering for expired_on
        if (! empty($params['expired_on_from'])) {
            $query->whereDate('expired_on', '>=', $params['expired_on_from']);
        }
        if (! empty($params['expired_on_to'])) {
            $query->whereDate('expired_on', '<=', $params['expired_on_to']);
        }

        // FlexSearch for Code
        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['code'];

        $query = $flexSearch->apply($query, [], $searchTerm, $searchableColumns);

        // Sorting
        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('code', 'asc');
                break;
            case 'z-a':
                $query->orderBy('code', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Store a newly created coupon.
     */
    public function storeCoupon(array $data): Coupon
    {
        $data['status'] = $data['status'] ?? true;

        return Coupon::create($data);
    }

    /**
     * Update the specified coupon.
     */
    public function updateCoupon(Coupon $coupon, array $data): Coupon
    {
        $coupon->update($data);

        return $coupon;
    }

    /**
     * Delete the specified coupon.
     */
    public function deleteCoupon(Coupon $coupon): void
    {
        $coupon->delete();
    }

    /**
     * Toggle the status of a coupon.
     */
    public function toggleStatus(Coupon $coupon): bool
    {
        $coupon->status = ! $coupon->status;

        return $coupon->save();
    }

    /**
     * Find coupon by code.
     */
    public function getCouponByCode(string $code): ?Coupon
    {
        return Coupon::where('code', $code)->first();
    }

    /**
     * Validate a coupon for application.
     */
    public function validateCoupon(?Coupon $coupon, float $totalAmount): array
    {
        if (! $coupon) {
            return ['valid' => false, 'message' => 'Invalid coupon code.'];
        }

        $userId = auth()->id();

        if (! $coupon->isValid($userId)) {
            return ['valid' => false, 'message' => 'This coupon is expired, inactive or usage limit reached.'];
        }

        if ($totalAmount < $coupon->min_spend) {
            return [
                'valid' => false,
                'message' => 'Minimum spend of '.number_format($coupon->min_spend, 2).' required to use this coupon.',
            ];
        }

        return ['valid' => true];
    }

    /**
     * Calculate discount for a coupon.
     */
    public function calculateDiscount(Coupon $coupon, float $subtotal, float $shippingCharge): float
    {
        $discount = 0;
        $baseAmount = $coupon->apply_for === 'total_product_price' ? $subtotal : $shippingCharge;

        if ($coupon->discount_type === 'percentage') {
            $discount = ($baseAmount * $coupon->discount_amount) / 100;
            if ($coupon->max_discount_amount && $discount > $coupon->max_discount_amount) {
                $discount = $coupon->max_discount_amount;
            }
        } else {
            $discount = $coupon->discount_amount;
        }

        // Discount cannot exceed the base amount it's applied to
        return min($discount, $baseAmount);
    }

    /**
     * Record usage of a coupon.
     */
    public function recordUsage(Coupon $coupon, Order $order): void
    {
        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id' => $order->user_id,
            'user_name' => $order->name,
            'user_email' => $order->email,
            'order_id' => $order->id,
            'discount_amount' => $order->discount,
        ]);

        $coupon->increment('used_count');
    }

    /**
     * Get usage history for a specific coupon.
     */
    public function getUsageHistory(Coupon $coupon, int $perPage = 10): LengthAwarePaginator
    {
        return CouponUsage::where('coupon_id', $coupon->id)
            ->with(['order', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get all active coupons with their eligibility for a specific subtotal.
     */
    public function getActiveCouponsWithEligibility(float $subtotal, ?int $userId = null): array
    {
        $today = now()->toDateString();
        $coupons = Coupon::where('status', true)
            ->whereDate('active_on', '<=', $today)
            ->whereDate('expired_on', '>=', $today)
            ->get();

        return $coupons->map(function ($coupon) use ($subtotal) {
            $isEligible = true;
            $reason = null;

            // Usage Limit Check
            if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
                $isEligible = false;
                $reason = 'Usage limit reached for this coupon.';
            }

            // Min Spend Check
            if ($isEligible && $subtotal < $coupon->min_spend) {
                $isEligible = false;
                $reason = 'Minimum spend of $'.number_format($coupon->min_spend, 2).' required.';
            }

            return [
                'coupon' => $coupon,
                'is_eligible' => $isEligible,
                'ineligible_reason' => $reason,
            ];
        })->toArray();
    }
}
