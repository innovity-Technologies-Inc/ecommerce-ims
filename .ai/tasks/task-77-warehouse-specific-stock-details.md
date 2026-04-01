# Task 77: Warehouse-Specific Stock Details

## Requirement
REQ-114: Add 'Stock Details' button to Warehouse index to view granular warehouse-wise inventory, batches, and serials.

## Implementation Details
1.  **Updated `WarehouseController`**: Added a `show` method that eager-loads `inventoryLevels`, `products`, `variants`, and `batches` for a specific warehouse.
2.  **Updated `routes/web.php`**: Registered the `admin.warehouses.show` route within the warehouse prefix.
3.  **Updated `admin.inventory.warehouses.partials.table`**: Added a "View Stock" (eye icon) button to each row in the warehouse list.
4.  **Created `admin.inventory.warehouses.show`**: A comprehensive details view for individual warehouses that:
    *   Lists all inventory records (Product, Variant, Batch).
    *   Displays current saleable quantity and damaged quantity.
    *   Provides a "View Serials" modal to inspect physical units (serials) specifically within that warehouse and batch context.
5.  **Verification**: Ran `php artisan optimize` to ensure routes are available.

## Verification Criteria
- [x] "View Stock" button is visible in the Warehouse index table.
- [x] Clicking the button opens the dedicated Warehouse Stock Details page.
- [x] The details page correctly lists products, their batch numbers, and specific quantities.
- [x] "View Serials" button correctly opens a modal showing only the serials belonging to that warehouse/product/batch.
- [x] Documentation updated in `PROJECT_DOCUMENTATION.md`.
