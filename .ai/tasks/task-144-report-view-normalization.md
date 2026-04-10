# Task: Report View Data Normalization (REQ-144)

## Objective
Ensure that all UI-based reports (Warehouse Performance, Sales, Stock, Inventory) consistently display `0` for numeric fields and appropriate placeholders (e.g., 'N/A') for missing text fields, preventing blank cells in the browser.

## Implementation Steps

### 1. Service Layer Hardening
- **File:** `app/Services/WarehousePerformanceService.php`
    - Review `calculateWarehouseMetrics` to ensure all array keys are always initialized with at least `0` or `(int)0` / `(float)0`.
- **File:** `app/Services/ReportService.php`
    - Review `getInventoryReport`, `getSalesSummary`, and `getStockReport` to ensure summary totals and breakdown maps use `COALESCE` or `?? 0` for all numeric metrics.

### 2. View Layer Fallbacks
- **Files:**
    - `resources/views/admin/reports/warehouse-performance/index.blade.php`
    - `resources/views/admin/reports/warehouse-performance/show.blade.php`
    - `resources/views/admin/reports/sales.blade.php`
    - `resources/views/admin/reports/stock.blade.php`
    - `resources/views/admin/reports/inventory.blade.php`
- **Changes:**
    - Use `{{ $row['field'] ?? 0 }}` for numeric values not wrapped in `number_format`.
    - Use `{{ $row['name'] ?? 'N/A' }}` or similar for descriptive fields.
    - Ensure `number_format` is always passed a valid number (e.g., `number_format($val ?? 0)`).

### 3. Verification
- Verify all report dashboards and "View All" pages.
- Confirm that no cells are completely empty.

## Verification & Testing
- Manual UI check.
- Run `php artisan optimize`.
- Mark REQ-144 as completed.
