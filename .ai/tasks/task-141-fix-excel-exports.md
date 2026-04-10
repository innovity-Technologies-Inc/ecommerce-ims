# Task-141: Fix Excel Exports

## Objective
Ensure all Excel exports in `ReportController` (Stock, Sales, Inventory) retrieve the full dataset matching current filters, regardless of the active UI page.

## Implementation Steps

### 1. Service Layer Refinement
- **File:** `app/Services/ReportService.php`
- **Change:** Update default `$perPage` parameter values from fixed integers to `null` to ensure `get()` is called by default unless pagination is explicitly requested.
    - `getStockMovements`
    - `getBatchAging`
    - `getSerialTrace`

### 2. Controller Export Refinement
- **File:** `app/Http/Controllers/Admin/ReportController.php`
- **Change:** Pass `null` for `$perPage` in all export methods to bypass pagination logic.
    - `exportStock`: Update calls to `getStockMovements`, `getBatchAging`, and `getSerialTrace`.
    - `exportSales`: Ensure `null` is passed to `getSalesSummary` and `getSalesByEntity`.
    - `exportInventory`: Ensure `null` is passed to `getInventoryReport`.

### 3. Verification
- **Test Case:** Filter a report, navigate to page 2, then click "Export".
- **Success Criteria:** Downloaded file contains all filtered records, not just page 2.
- **Styling:** Run `./vendor/bin/pint --dirty`.
- **Optimization:** Run `php artisan optimize`.

## Documentation Update
- Update `PROJECT_DOCUMENTATION.md` to reflect that exports now correctly retrieve full data matching active filters.
