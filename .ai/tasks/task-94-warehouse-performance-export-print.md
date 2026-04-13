# Task: Warehouse Performance Report Export & Print

Implement "Excel Export" and "Print" functionality for the Warehouse Performance report to align it with other system reports.

## Requirements
- [x] Implement Excel Export for the Warehouse Performance Index view (all warehouses summary).
- [x] Implement Excel Export for the Warehouse Performance Show view (single warehouse detailed metrics).
- [x] Add "Print" button with `printReportCard` JS integration for both views.
- [x] Create dedicated Export classes if required.
- [x] Add necessary routes and controller methods.

## Implementation Steps

### 1. Backend: Service & Export Logic
- [x] Update `WarehousePerformanceService` to support data retrieval for exports.
- [x] Create `WarehousePerformanceExport` class in `app/Exports/Admin/`.

### 2. Routes
- [x] Add `admin.reports.warehouse-performance.export` route in `routes/web.php`.

### 3. Controller
- [x] Add `export()` method to `WarehousePerformanceController`.

### 4. Frontend: Views
- [x] Update `resources/views/admin/reports/warehouse-performance/index.blade.php`:
    - Add "Export" button.
    - Add "Print" button with `printReportCard` integration.
- [x] Update `resources/views/admin/reports/warehouse-performance/show.blade.php`:
    - Add "Export" button.
    - Add "Print" button.

## Verification Criteria
- [x] Excel file downloads with correct columns and data for both summary and detail views.
- [x] Print window opens with correctly formatted report content.
- [x] All filters (date range, warehouse) are respected during export.
- [x] UI matches the styling of Sales/Inventory reports.
