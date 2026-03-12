<?php

namespace App\Http\Controllers;

use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function __construct(protected CouponService $couponService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $coupons = $this->couponService->getAllCoupons($request->all());

        if ($request->ajax()) {
            return view('admin.coupons.partials.table', compact('coupons'))->render();
        }

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.coupons.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CouponRequest $request): RedirectResponse
    {
        $this->couponService->storeCoupon($request->validated());

        return redirect()->route('admin.coupons.index')->with([
            'message' => 'Coupon created successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.form', compact('coupon'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $this->couponService->updateCoupon($coupon, $request->validated());

        return redirect()->route('admin.coupons.index')->with([
            'message' => 'Coupon updated successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon): RedirectResponse
    {
        $this->couponService->deleteCoupon($coupon);

        return redirect()->route('admin.coupons.index')->with([
            'message' => 'Coupon deleted successfully',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Toggle the status of a coupon.
     */
    public function toggleStatus(Coupon $coupon): JsonResponse
    {
        $this->couponService->toggleStatus($coupon);

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon status updated successfully',
        ]);
    }
}
