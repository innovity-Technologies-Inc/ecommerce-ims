# Task 69: Warehouse Stock Limit Separation

Move warehouse-specific minimum stock limits from `inventory_levels` to a dedicated `warehouse_stock_limits` table and update the product form and dashboard to reflect this architectural change.

## Requirements
- **REQ-104:** Warehouse Stock Limit Separation: Move warehouse-specific minimum stock limits from `inventory_levels` to a dedicated `warehouse_stock_limits` table. Update product form to allow assigning specific limits via a modal. Update dashboard low-stock logic.

## Implementation Steps

### 1. Database Schema
- Create `WarehouseStockLimit` model and migration (`warehouse_stock_limits` table).
  - Fields: `id`, `product_id`, `product_variant_id` (nullable), `warehouse_id`, `min_stock`.
- Update `inventory_levels` table (migration):
  - Drop `min_stock_override` and `last_alert_sent`.

### 2. Model Updates
- Define relationships in `WarehouseStockLimit` (belongsTo Product, ProductVariant, Warehouse).
- Update `Product`, `ProductVariant`, and `Warehouse` models to add `hasMany(WarehouseStockLimit::class)`.
- Update `InventoryLevel` to remove dropped fields from `$fillable`.

### 3. Service Layer Refactoring
- **`ProductService`**: 
  - Update `updateInventoryOverrides()` to sync data with the new `warehouse_stock_limits` table instead of `inventory_levels`.
- **`DashboardService`**: 
  - Refactor `getLowStockProducts()` to calculate warehouse low stock by joining/querying against `warehouse_stock_limits` and `inventory_levels`.

### 4. UI/UX Refinement
- **Product Form (`form.blade.php`)**:
  - Remove the inline table that depended on existing inventory levels.
  - Implement a "Set Warehouse Limits" button for base products (when no variants) and for each variant row.
  - Create a Bootstrap modal containing a searchable list/select of all active warehouses and an input for `min_stock`.
  - Use JavaScript to dynamically store selected warehouse limits in hidden inputs so they submit with the main product form.

## Verification Criteria
- Warehouse stock limits can be set *before* any inventory exists.
- The `warehouse_stock_limits` table correctly stores these configurations.
- The Dashboard accurately reports low stock based on the new table logic.
- The Product Form handles adding, updating, and removing warehouse limits seamlessly via the modal.
