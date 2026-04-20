# Task 199: Guest Customer Counter in Reports

Implement a guest customer counter in the Customer Purchase Reports overview dashboard.

## Requirement Reference
- **REQ-199:** Guest Customer Counter in Reports.

## Implementation Steps

### 1. Service Layer Update
- **File:** `app/Services/CustomerReportService.php`
- **Action:** Update `getOverviewStats` method to calculate guest customers.
- **Logic:** `Order::whereNull('user_id')->distinct('email')->count('email')` within the provided date range.

### 2. View Update
- **File:** `resources/views/admin/reports/customers/index.blade.php`
- **Action:** Add a new metric card to display the "Guest Customers" count.

### 3. Verification
- **Action:** Run `php artisan optimize` to refresh caches.
- **Verification:** 
    - Verify that the counter correctly shows the number of unique guest emails from the `orders` table.
    - Ensure the count respects the date range filter.

## Documentation Update
- Update `PROJECT_DOCUMENTATION.md` to reflect the new guest customer metric in the Customer Reports module.
