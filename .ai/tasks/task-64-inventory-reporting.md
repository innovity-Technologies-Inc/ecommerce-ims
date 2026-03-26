# Task 64: Inventory Reporting Module (Stock & Batches)

## 1. Requirement
Implement a comprehensive inventory reporting system within the Admin Panel. This module will consist of two primary sections:
- **Stock Report:** Displays real-time stock levels for all products and variants across different warehouses.
- **Batch Report:** Provides a batch-wise view of received inventory, with the ability to drill down into batch items and physical serial numbers.

- **REQ-97:** Inventory Stock Report.
- **REQ-98:** Batch Tracking Report.

## 2. Implementation Steps

### 1. Controller & Service Layer
- **InventoryReportController:** Create a new controller to handle the reporting routes.
- **InventoryService:** Add methods to:
    - `getStockReport(array $params)`: Retrieve paginated inventory levels with warehouse and product details.
    - `getBatchReport(array $params)`: Retrieve paginated batches with their items and serial counts.
    - `getBatchDetails(Batch $batch)`: Retrieve detailed information about a specific batch, including its items and serial numbers.

### 2. Routing
- Define new routes in `routes/web.php` under the `admin.inventory` prefix:
    - `admin/inventory/stock` (Index)
    - `admin/inventory/batches` (Index)
    - `admin/inventory/batches/{batch}` (Show)

### 3. Frontend (Admin Panel)
- **Stock Index View:**
    - Table columns: #, Product, Variant, Warehouse, Batch No, Current Stock, Min Stock Override.
    - Product name should link to the existing Admin Product Details page.
    - Support for search (Product/Variant name) and filtering (Warehouse).
- **Batch Index View:**
    - Table columns: #, Batch Number, PO Number, Warehouse, Total Items, Created At.
    - "Action" column with a "View Details" button.
- **Batch Detail View:**
    - Display Batch Header info (Batch No, PO, Warehouse).
    - Table of Batch Items (Product, Variant, Quantity).
    - Section for Physical Serial Numbers linked to this batch (if any).

### 4. Sidebar Integration
- Add "Inventory" parent menu to the sidebar (if not already present).
- Add "Stock Report" and "Batch Report" sub-menus.

### 5. Finalization
- Run `./vendor/bin/pint --dirty`.
- Run `php artisan optimize`.
- Update `PROJECT_DOCUMENTATION.md`.

## 3. Verification Criteria
- [x] Stock report correctly shows quantities per warehouse and batch.
- [x] Clicking a product in the stock report redirects to its admin details page.
- [x] Batch report lists all PO receipts with their shared batch numbers.
- [x] Batch details view correctly lists all products and their quantities within that batch.
- [x] Serial numbers are viewable within the batch details view.
- [x] Search and filtering work across reporting pages.
- [x] All new permissions (inventory, po, warehouse, supplier) are available for selection in the Role Management form.
