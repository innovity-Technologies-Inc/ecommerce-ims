# Task 67: Warehouse Index Refinement

Refine the warehouse index page to explicitly show the quarantine flag and add a dedicated filter for warehouse types (Normal vs. Quarantine).

## Requirements
- **REQ-101:** Warehouse Index Refinement: Explicitly show the Quarantine flag in the warehouse list and add a dedicated filter to toggle between Normal and Quarantine warehouses.

## Implementation Steps

### 1. Service Layer Update
- **File:** `app/Services/InventoryService.php`
- **Method:** `getAllWarehouses`
- **Action:** Update the method to accept and apply an `is_quarantine` filter using FlexSearch.

### 2. UI Refinement (Index Page)
- **File:** `resources/views/admin/inventory/warehouses/index.blade.php`
- **Action:** 
    - Add a "Type" dropdown filter (All, Normal, Quarantine).
    - Update the JavaScript `fetchWarehouses` function to include the `is_quarantine` parameter in the AJAX request and URL state.

### 3. UI Refinement (Table Partial)
- **File:** `resources/views/admin/inventory/warehouses/partials/table.blade.php`
- **Action:** Ensure the "Quarantine" column is consistently styled and the badges (Yes/No) are clearly visible. (Verified: Already present, will double-check styling).

### 4. Verification
- **Action:** Run `php artisan optimize` to refresh caches.
- **Manual Check:**
    - Verify that the "Type" filter correctly filters the warehouse list.
    - Verify that the "Quarantine" flag is visible in the table.
    - Verify that AJAX searching and sorting still work correctly with the new filter.

## Verification Criteria
- Warehouse list shows all warehouses by default.
- Selecting "Normal" shows only warehouses where `is_quarantine` is false.
- Selecting "Quarantine" shows only warehouses where `is_quarantine` is true.
- The "Quarantine" column is present in the table with "Yes" (Red) and "No" (Success) badges.
- URL is updated correctly when the filter is changed.
- Pagination preserves the filter state.
