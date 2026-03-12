# Task 33: Coupon Management Module

Implement a Coupon module under the "Promotions" section of the admin panel.

## Requirement
REQ-49: Coupon Management Module (Admin CRUD, code generation, discount logic, usage limits, and date range filtering).

## Steps

### 1. Database Setup
- [x] Create `Coupon` model and migration.
- [x] Create `CouponSeeder`.

### 2. Backend Implementation (Service Layer)
- [x] Create `CouponRequest` for validation.
- [x] Create `CouponService` for business logic.
- [x] Create `CouponController` (Admin).
- [x] Register routes in `routes/web.php` under admin prefix.

### 3. Frontend Implementation (Admin Panel)
- [x] Update sidebar to include "Promotions" and "Coupons".
- [x] Create Coupon Index view with listing, searching, and filtering.
- [x] Create Coupon Create view.
- [x] Create Coupon Edit view.
- [x] Implement AJAX toggle for status.

### 4. Verification
- [x] Run migrations and seeders.
- [x] Verify CRUD functionality.
- [x] Verify search and filter functionality.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- Admin can create, read, update, and delete coupons.
- Coupons can be searched by code.
- Coupons can be filtered by status, application area, and date ranges.
- Form validation correctly handles required fields and logical constraints (e.g., max discount for percentage).
- Sidebar correctly displays the new module.
