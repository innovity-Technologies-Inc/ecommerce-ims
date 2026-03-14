# Task 42: Coupon Usage History tracking

Implement a dedicated administrative page to track the detailed audit trails of specific coupon applications, providing transparency on which customers used which codes and the resulting financial impact.

## Requirements
- REQ-67: Coupon Usage History (Detailed audit trails).

## Steps

### 1. Service Layer Implementation
- [x] Add `CouponService::getUsageHistory()` method.
- [x] Implement eager-loading for `order` and `user` relationships to optimize performance.
- [x] Add server-side pagination for usage records.

### 2. Controller & Routing
- [x] Add `CouponController::usageHistory()` method.
- [x] Register the `admin.coupons.history` route in `routes/web.php`.

### 3. Frontend Implementation (Admin Panel)
- [x] Create `resources/views/admin/coupons/usage-history.blade.php`.
- [x] Implement high-level summary cards (Type, Amount, Total Used, Usage Limit).
- [x] Build a detailed table showing Customer, Order ID (linked), Discount Applied, and Timestamp.
- [x] Add a "History" icon to the main Coupon List actions column.

### 4. Verification
- [x] Verify that clicking the history icon leads to the correct coupon's history.
- [x] Verify that order links lead correctly to the Order Details page.
- [x] Ensure pagination works correctly for coupons with high usage.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Update `PROJECT_DOCUMENTATION.md` and `requirements.md`.

## Verification Criteria
- Administrators have a clear, unalterable record of all coupon redemptions.
- The UI is consistent with the rest of the admin panel (Bootstrap 5/Solar icons).
- Navigation between history, coupons, and orders is seamless.
