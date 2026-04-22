# Task: Standardize Table Serial Numbers (REQ-221)

Standardize the implementation of serial numbers in administrative tables to use the `HelperClass::indexNumberSerialization` method correctly.

## 1. Requirement Detail
- **What:** Ensure all tables in the admin panel use the mandatory `$sl++` pattern for row numbering.
- **How:** Fix `resources/views/admin/inventory/warehouses/partials/table.blade.php` and `resources/views/admin/faqs/partials/table.blade.php`.
- **Verification:** Row numbers should increment correctly across paginated pages.

## 2. Implementation Steps

### Step 1: Fix Warehouse Table
- Update `resources/views/admin/inventory/warehouses/partials/table.blade.php`:
    - Change `{{$sl}}` to `{{$sl++}}`.

### Step 2: Fix FAQ Table
- Update `resources/views/admin/faqs/partials/table.blade.php`:
    - Initialize `$sl` before the loop and use `{{$sl++}}` inside the loop.

### Step 3: Global Verification
- Verify other partial tables to ensure consistency.

### Step 4: Finalize
- Run `./vendor/bin/pint --dirty`.
- Run `php artisan optimize`.

## 3. Verification Criteria
- [x] Warehouse table correctly increments serial numbers.
- [x] FAQ table correctly increments serial numbers.
- [x] All other administrative tables follow the same pattern.
