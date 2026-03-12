# Task 34: Client-side Coupon Application & Tracking

Implement coupon application on the checkout page with AJAX and track usage in a history table.

## Requirements
- REQ-50: Client-side Coupon Application (AJAX-based application on checkout page, instant price updates).
- REQ-51: Coupon Usage Tracking (History table tracking User ID, Email, Name, and total usage limits).

## Steps

### 1. Database Setup
- [x] Create `CouponUsage` model and migration.
- [x] Create migration for adding `coupon_id` to `orders` table.

### 2. Backend Implementation (Service Layer)
- [x] Update `CouponService` to include:
    - `validateCoupon(string $code, float $totalAmount, ?int $userId)`: Checks code, status, dates, min spend, and usage limits.
    - `applyCoupon(string $code, float $totalAmount, float $shippingCost)`: Calculates discount based on `apply_for` and `discount_type`.
    - `recordUsage(Coupon $coupon, Order $order)`: Records the usage in `coupon_usages` table and increments `used_count` in `coupons` table.
- [x] Create `CouponApplyController` for client-side AJAX requests.

### 3. Frontend Implementation (Checkout Page)
- [x] Update Checkout view to include Coupon Input field and Apply button.
- [x] Implement AJAX logic to:
    - Send coupon code to server.
    - Update order summary (Discount, Grand Total) instantly upon success.
    - Show success/error messages via Toastr.

### 4. Integration with Order Placement
- [x] Update `OrderService::placeOrder()` to:
    - Verify and apply the coupon again during final order creation.
    - Store the applied coupon ID and discount amount in the `orders` table.
    - Call `recordUsage()` after successful order placement.

### 5. Verification
- [x] Verify coupon application with various scenarios.
- [x] Verify usage tracking in the database.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- Coupons correctly apply discounts to total or shipping.
- Discounts are updated instantly on the UI without reload.
- Users cannot exceed coupon usage limits.
- Usage is correctly recorded with user details.
- Invalid coupons show clear error messages.
