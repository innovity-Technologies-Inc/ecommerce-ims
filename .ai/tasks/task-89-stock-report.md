# Task 89: Stock Reporting Module

Implement a comprehensive Stock Reporting dashboard in the Admin Panel with detailed metrics, derived report tabs, and granular filtering.

## Requirements

### Main Columns
- **Logistics:** Warehouse, Product, Variant, SKU, Batch, Supplier.
- **Quantities:** Current quantity, Damaged quantity, Available quantity.
- **Alerts:** Min stock threshold, Low stock flag.
- **Financials:** Unit cost, Inventory value.
- **Temporal:** Last movement date.

### Derived Report Tabs
1. **Stock Movement:** Detailed audit trail from `stock_ledgers`.
2. **Batch Aging:** Analysis of inventory age based on receipt date.
3. **Damaged Stock:** Specific view for items marked as damaged/wastage.
4. **Serial Trace:** Tracking individual physical units from `batch_serials`.

### Filters
- Warehouse, Product/Category/Brand, Supplier, Batch.
- Stock Status, Low Stock Only.
- Date Range (specifically for movement history).

### Formulas
- `Available Qty = current_quantity`
- `Stock Value = available_qty * unit_cost`
- `Days since last movement = Today - Max(stock_ledgers.created_at)`

## Implementation Plan

### 1. Service Layer (`app/Services/ReportService.php`)
- Implement `getStockReport(array $filters, ?int $perPage = null)`:
    - Join `inventory_levels`, `batch_products`, `batches`, `products`, `product_variants`, and `warehouse_stock_limits`.
    - Calculate "Last Movement Date" using a subquery on `stock_ledgers`.
    - Handle "Low Stock" logic by comparing `current_quantity` with `min_stock` (Warehouse-specific) or `min_stock_global`.
- Implement `getStockMovements(array $filters, int $perPage = 20)`:
    - Query `stock_ledgers` with full relationships.
- Implement `getBatchAging(array $filters, int $perPage = 20)`:
    - Calculate days between `batches.created_at` and now.
- Implement `getSerialTrace(array $filters, int $perPage = 20)`:
    - Query `batch_serials` with status and movement history.

### 2. Controller (`app/Http/Controllers/Admin/ReportController.php`)
- Implement `stock(Request $request)`:
    - Orchestrate the multi-tab data retrieval.
- Implement `exportStock(Request $request)`:
    - Handle Excel exports for all tabs.

### 3. UI Implementation (`resources/views/admin/reports/stock.blade.php`)
- **Tabs Interface:** Use Bootstrap 5 Nav-tabs for Main, Movement, Aging, Damaged, and Serial Trace.
- **Summary Cards:** Total Units, Damaged Units, Low Stock Items, Total Value.
- **Filters:** Integrated filter bar matching the Sales/Inventory design.
- **Pagination & View All:** Limit dashboard tables to 10 rows with "View All" functionality.

### 4. Routing & Sidebar
- Register routes and add "Stock Report" under the Reports section in the sidebar.

## Verification Criteria
- [x] Low stock flag correctly triggers based on thresholds.
- [x] Valuation matches `quantity * unit_cost` from batch records.
- [x] Movement report correctly reflects ledger entries.
- [x] "View All" and Pagination work for all tabs.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.
