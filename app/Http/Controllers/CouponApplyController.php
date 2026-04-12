<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CouponApplyController extends Controller
{
    public function __construct(
        protected CouponService $couponService,
        protected CartService $cartService
    ) {}

    /**
     * Apply a coupon to the current cart.
     */
    public function apply(Request $request): JsonResponse
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        $code = $request->coupon_code;
        $subtotal = $this->cartService->getCartTotal();
        $shippingCharge = (float) Session::get('shipping_charge', 0);

        $coupon = $this->couponService->getCouponByCode($code);
        $validation = $this->couponService->validateCoupon($coupon, $subtotal);

        if (! $validation['valid']) {
            return response()->json([
                'status' => 'error',
                'message' => $validation['message'],
            ]);
        }

        $discount = $this->couponService->calculateDiscount($coupon, $subtotal, $shippingCharge);

        Session::put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'discount' => $discount,
            'apply_for' => $coupon->apply_for,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon applied successfully!',
            'discount' => number_format($discount, 2),
            'grand_total' => number_format($subtotal + $shippingCharge - $discount, 2),
        ]);
    }

    /**
     * Remove the applied coupon.
     */
    public function remove(): JsonResponse
    {
        Session::forget('coupon');

        $subtotal = $this->cartService->getCartTotal();
        $shippingCharge = (float) Session::get('shipping_charge', 0);

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon removed.',
            'grand_total' => number_format($subtotal + $shippingCharge, 2),
        ]);
    }

    /**
     * Get available coupons with eligibility status.
     */
    public function availableCoupons(): JsonResponse
    {
        $subtotal = $this->cartService->getCartTotal();
        $userId = auth()->id();

        $coupons = $this->couponService->getActiveCouponsWithEligibility($subtotal, $userId);

        return response()->json($coupons);
    }
}
