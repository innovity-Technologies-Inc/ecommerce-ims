# Task-153: Available Coupons Modal

## Objective
Implement an "Available Coupons" button and modal on the checkout page. The modal will list all currently active coupons, highlighting those eligible for the current cart subtotal and greying out ineligible ones with a specific reason. Applying a coupon from the modal will automatically fill the input and apply it. Also ensure coupons can have an infinite usage limit (nullable).

## Implementation Steps

### 1. Backend Logic
- [x] **File:** `app/Services/CouponService.php`
  - Add `getActiveCouponsWithEligibility(float $subtotal, ?int $userId)`:
    - Query `Coupon::where('status', true)`
    - Filter by `active_on` <= today and `expired_on` >= today.
    - Map over results: Check `usage_limit` and `min_spend` against `$subtotal`.
    - Return an array of objects/arrays with `coupon`, `is_eligible` (boolean), and `ineligible_reason` (string).
- [x] **File:** `app/Http/Controllers/CouponApplyController.php`
  - Add `availableCoupons()` method returning a JSON response with the data from `CouponService`.
- [x] **File:** `routes/web.php`
  - Add `Route::get('/available-coupons', [CouponApplyController::class, 'availableCoupons'])->name('checkout.available_coupons');` inside the `checkout` prefix group.
- [x] **File:** `app/Http/Requests/Admin/CouponRequest.php`
  - Update `prepareForValidation` to handle nullable `usage_limit` (Allow infinite usage).

### 2. Frontend UI & Logic
- [x] **File:** `resources/views/client/checkout/index.blade.php`
  - **Button:** Add a `<button type="button" class="btn btn-link btn-sm text-decoration-none mt-2" data-bs-toggle="modal" data-bs-target="#availableCouponsModal"><i class="bx bx-list-ul"></i> View Available Coupons</button>` below the coupon input field.
  - **Modal Structure:** Add a standard Bootstrap 5 modal to the bottom of the view. The body will contain a `div#available-coupons-list`.
  - **JavaScript:**
    - On modal show event (`show.bs.modal`), trigger an AJAX GET request to `checkout.available_coupons`.
    - Render the returned coupons in the modal body:
      - **Eligible:** Standard styling, green/primary "Apply" button.
      - **Ineligible:** `opacity-50` styling, disabled "Apply" button, and `<small class="text-danger">` showing the `ineligible_reason`.
    - Add click listener for the modal's "Apply" buttons:
      - Set the value of `#coupon_code`.
      - Trigger the click event on `#apply-coupon-btn`.
      - Close the modal.

## Verification & Testing
- [x] Navigate to the checkout page with items in the cart.
- [x] Click "View Available Coupons". Verify the modal opens and populates via AJAX.
- [x] Verify coupons with a `min_spend` higher than the cart subtotal are greyed out and show a specific error message.
- [x] Verify eligible coupons have an active "Apply" button.
- [x] Click "Apply" on an eligible coupon. Verify it closes the modal, fills the input, and successfully applies the discount.
- [x] Verify only one coupon is applied at a time (existing behavior).
