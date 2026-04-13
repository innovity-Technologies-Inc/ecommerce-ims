# Task 88: Inventory Level & Valuation Reporting

Implement a comprehensive inventory reporting dashboard in the Admin Panel to track stock levels, valuation, and historical snapshots with granular filtering.

## Requirements

### Metrics & Data
- **Current Stock:** Real-time stock levels across all warehouses.
- **As-of Date Stock:** Historical stock levels calculated by replaying `stock_ledgers`.
- **Valuation:** Total cost value of inventory (`quantity * unit_cost` from `batch_products`).
- **Damaged Stock:** Optional inclusion of damaged/wastage items.
- **Entity Breakdowns:** 
    - Summary totals (Global).
    - Warehouse-wise valuation.
    - Product-wise valuation.
    - Batch-wise valuation.

### Filters
- **As-of Date:** Calculate stock as it was at the end of this date.
- **Warehouse:** Single warehouse filter.
- **Supplier:** Filter by products sourced from a specific supplier.
- **Product/Category/Brand:** Drill down into specific catalog segments.
- **Include Damaged:** Toggle to include `wastage`/`damaged` status serials/quantities.
- **Batch:** Filter by specific batch numbers.

## Implementation Plan

### 1. Service Layer (`app/Services/ReportService.php`)
- Implement `getInventoryReport(array $filters)`:
    - **Historical Logic:** If `as_of_date` is provided, calculate stock by summing `change_qty` from `stock_ledgers` up to that date.
    - **Current Logic:** Use `inventory_levels` and `batch_products` for real-time data if no date is provided.
    - **Valuation Logic:** Join with `batch_products` to get the correct `unit_cost` for each batch.
    - **Entity Grouping:** Support dynamic grouping for the output sections (Warehouse, Product, Batch).

### 2. Controller (`app/Http/Controllers/Admin/ReportController.php`)
- Implement `inventory(Request $request)`:
    - Retrieve inventory data and filter options.
    - Pass data to the view.
- Implement `exportInventory(Request $request)`:
    - Export inventory report to Excel.

### 3. UI Implementation (`resources/views/admin/reports/inventory.blade.php`)
- **Summary Cards:** Total Items, Total Saleable Qty, Total Valuation.
- **Filters Bar:** Date picker, Warehouse/Supplier/Category dropdowns.
- **Breakdown Cards:** Use the "New Window DOM" print method and Excel export for:
    - Warehouse-wise Table.
    - Product-wise Table.
    - Batch-wise Table.

### 4. Routing (`routes/web.php`)
- Register `admin.reports.inventory` and `admin.reports.inventory.export`.

## Verification Criteria
- [x] Valuation correctly uses `unit_cost` from the specific `batch_products` record.
- [x] "As-of date" calculation correctly aggregates `stock_ledgers`.
- [x] Filtering by Supplier correctly identifies batches linked to that supplier.
- [x] UI allows per-card printing and excel export.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.
