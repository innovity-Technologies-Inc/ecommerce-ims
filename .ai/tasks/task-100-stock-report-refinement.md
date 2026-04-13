# Task: Stock Report View-All Refinement (REQ-140)

Implement full-data "Print" and "Excel Export" for all detailed stock report views. Ensure exports match the current view and filters.

## 1. Requirement Logging
- [x] Log REQ-140 in `requirements.md`.

## 2. Implementation Steps

### Backend: ReportController & Service
- [x] Update `ReportController::exportStock` to:
    - Support the `view` parameter (matching the dashboard's "View All" links).
    - Implement cases for all stock report types: `warehouse`, `product`, `batch`, `movement`, `aging`, `wastage_product`, `wastage_warehouse`, `wastage_batch`, `serial`.
    - Fetch data using `ReportService` with current filters, but without pagination (for full export).
    - Map data correctly for Excel headings.

### Frontend: Stock Report View
- [x] Update `resources/views/admin/reports/stock.blade.php`:
    - Ensure the "Export" button in the "View All" section passes the `view` parameter.
    - Update `printFullReport()` or the print logic to ensure it's "clean" and includes proper business headers (Business Name, Report Title, Date).
    - Ensure the printed table has correct headers matching the view type.

## 3. Verification Criteria
- [x] Clicking "View All" on any stock report card shows the detailed view.
- [x] Clicking "Export" on the detailed view downloads an Excel file containing ALL matching records (not just the current page).
- [x] Excel file contains the correct columns and data for the specific view being exported.
- [x] Clicking "Print" on the detailed view opens a new tab with a clean, formatted report including business headers and ALL records.
- [x] All filters (Date, Warehouse, Supplier, etc.) are correctly applied to both Print and Export.
- [x] Verify using seeded data.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.

## 4. Documentation Update
- [x] Update `PROJECT_DOCUMENTATION.md` with the new export/print capabilities for Stock Reports.
