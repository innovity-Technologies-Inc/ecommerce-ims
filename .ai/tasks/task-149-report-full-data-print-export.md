# Task-149: Report Full Data Print & Export

## Objective
Ensure that "Print" and "Export" buttons in report dashboards (Sales, Stock, Inventory) always process the complete dataset matching the current filters, rather than being limited to the 10 preview rows shown on the dashboard.

## Implementation Steps

### 1. Unified Printing Strategy
- [x] **Views:** `stock.blade.php`, `sales.blade.php`, `inventory.blade.php`.
- [x] **Change:** Update the `printReportCard` function calls (or replace them) to instead call `printFullReport(viewName)`.
- [x] **Refactor:** Create a JavaScript function `printFullReport(view)` that:
    1. Takes the current URL.
    2. Appends/Updates the `view` parameter.
    3. Appends `is_print=1`.
    4. Opens in a new tab.
- This ensures the printer sees the dedicated print view (which already handles full data when `is_print` is present).

### 2. Full Data Export
- [x] **File:** `app/Http/Controllers/Admin/ReportController.php`
- [x] **Change:** In `exportStock`, `exportSales`, and `exportInventory`, ensure that when a specific `view` or `type` is passed from a dashboard card, the service is called with `perPage = null` to fetch all matching records. (The controller currently does this for `exportStock` but needs verification for all branches).

### 3. Verification
- [x] Filter a report to a set that has > 10 records.
- [x] Click "Print" on a dashboard card (e.g., "Stock by Warehouse"). Verify a new tab opens with the full list.
- [x] Click "Export" on the same card. Verify the Excel file contains all matching records, not just 10.
- [x] Run `php artisan optimize`.

## Verification Criteria
- [x] Printing from any dashboard card results in a full-page document of all filtered items.
- [x] Exporting from any dashboard card results in an Excel file with all filtered items.
- [x] No regression in "Dashboard Overview" visual limits (still shows 10 rows for UI cleaniness).
