<?php

namespace App\Services;

use App\Models\Coupon;
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
}
