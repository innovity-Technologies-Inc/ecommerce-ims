# Task 201: Split Customer Stats Rows

Reorganize the summary metrics in the Customer Reports overview dashboard to be displayed across two rows (3 + 2).

## Requirement Reference
- **REQ-201:** Split Customer Stats Rows.

## Implementation Steps

### 1. View Update
- **File:** `resources/views/admin/reports/customers/index.blade.php`
- **Action:** Split the single row of summary metrics into two separate rows.
- **Layout:**
    - Row 1: Total Customers, New Customers, Guest Customers (using `col-md-4`).
    - Row 2: Returning, Active (3M) (using `col-md-6`).

### 2. Verification
- **Action:** Run `php artisan optimize` to refresh caches.
- **Visual Check:** Verify the layout in the browser to ensure the cards are correctly sized and spaced across the two rows.

## Documentation Update
- No major documentation update required for this UI layout change, but ensure `PROJECT_DOCUMENTATION.md` remains consistent if applicable.
