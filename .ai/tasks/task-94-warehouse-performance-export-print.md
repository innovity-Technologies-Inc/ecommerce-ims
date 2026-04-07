# Task: Warehouse Performance Report Export & Print

Implement "Excel Export" and "Print" functionality for the Warehouse Performance report to align it with other system reports.

## Requirements
- [ ] Implement Excel Export for the Warehouse Performance Index view (all warehouses summary).
- [ ] Implement Excel Export for the Warehouse Performance Show view (single warehouse detailed metrics).
- [ ] Add "Print" button with `printReportCard` JS integration for both views.
- [ ] Create dedicated Export classes if required.
- [ ] Add necessary routes and controller methods.

## Implementation Steps

### 1. Backend: Service & Export Logic
- [ ] Update `WarehousePerformanceService` to support data retrieval for exports.
- [ ] Create `WarehousePerformanceExport` class in `app/Exports/Admin/`.

### 2. Routes
- [ ] Add `admin.reports.warehouse-performance.export` route in `routes/web.php`.

### 3. Controller
- [ ] Add `export()` method to `WarehousePerformanceController`.

### 4. Frontend: Views
- [ ] Update `resources/views/admin/reports/warehouse-performance/index.blade.php`:
    - Add "Export" button.
    - Add "Print" button with `printReportCard` integration.
- [ ] Update `resources/views/admin/reports/warehouse-performance/show.blade.php`:
    - Add "Export" button.
    - Add "Print" button.

## Verification Criteria
- [ ] Excel file downloads with correct columns and data for both summary and detail views.
- [ ] Print window opens with correctly formatted report content.
- [ ] All filters (date range, warehouse) are respected during export.
- [ ] UI matches the styling of Sales/Inventory reports.
