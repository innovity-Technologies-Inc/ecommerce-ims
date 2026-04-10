# Task-142: Report Filter Preservation

## Objective
Ensure that applying filters on detailed report pages (Sales, Inventory, Stock) preserves the current view instead of redirecting the user back to the main report dashboard.

## Implementation Steps

### 1. View Updates
- **Files:**
    - `resources/views/admin/reports/stock.blade.php`
    - `resources/views/admin/reports/sales.blade.php`
    - `resources/views/admin/reports/inventory.blade.php`
- **Change:** Add a hidden input field for the `view` parameter inside the main filter form.
```html
<input type="hidden" name="view" value="{{ $view ?? '' }}">
```

### 2. Controller Updates
- **File:** `app/Http/Controllers/Admin/ReportController.php`
- **Change:** Ensure the `stock`, `inventory`, and `sales` methods correctly extract the `view` parameter from the request and pass it back to the view, so the hidden input is correctly populated. (Verify existing logic first).

### 3. Verification
- **Test Case:** 
    1. Navigate to "Stock Reports".
    2. Click "View All" on any detailed section (e.g., "Stock Movement History").
    3. Apply a filter (e.g., select a Warehouse).
    4. Verify the page remains on the "Stock Movement History" view with the filter applied, instead of going back to the main dashboard.
- **Repeat for:** Sales and Inventory reports.
- **Styling:** Run `./vendor/bin/pint --dirty`.
- **Optimization:** Run `php artisan optimize`.

## Documentation Update
- Update `PROJECT_DOCUMENTATION.md` to reflect that report filters now correctly preserve the active detailed view.
