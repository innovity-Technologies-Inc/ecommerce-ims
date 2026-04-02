# Task 82: Inventory Costing Refinement (REQ-119)

Refactor the inventory system to move cost tracking from products/variants to batch level for more accurate FIFO/LIFO support and cleaner stock ledger.

## Requirements
- Remove `unit_cost` and `cost` columns from `stock_ledgers` table.
- Add `unit_cost` column to `batch_products` table.
- Remove `unit_cost` column from `products` and `product_variants` tables.
- Update Admin UI:
    - Hide `unit_cost` in product details view.
    - Show `unit_cost` in batch details page.

## Implementation Steps

### 1. Database Migration
- Create a migration to modify the tables as per requirements.
    - `stock_ledgers`: drop `unit_cost`, `cost`.
    - `batch_products`: add `unit_cost` (decimal 15,2).
    - `products`: drop `unit_cost`.
    - `product_variants`: drop `unit_cost`.

### 2. Model Updates
- `app/Models/StockLedger.php`: Update `$fillable` and `casts()`.
- `app/Models/BatchProduct.php`: Update `$fillable` and `casts()`.
- `app/Models/Product.php`: Update `casts()`.
- `app/Models/ProductVariant.php`: Update `casts()`.

### 3. Service & Logic Updates
- Search for usages of `unit_cost` in `app/Services` and update logic.
- Specifically check:
    - PO Receiving logic (where `batch_products` are created).
    - Stock Adjustment logic.
    - Inventory Reporting logic.

### 4. Admin UI Updates
- `resources/views/admin/product/show.blade.php`: Remove `unit_cost` display.
- `resources/views/admin/batch/show.blade.php` (or similar): Add `unit_cost` to the product list in batch details.

### 5. Verification
- Run migrations.
- Verify product creation/edit (should no longer have `unit_cost`).
- Verify PO receiving (should save `unit_cost` to `batch_products`).
- Verify stock reports.
- Run `php artisan optimize`.
- Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- [x] Database schema updated correctly.
- [x] `unit_cost` removed from Product forms and details.
- [x] `unit_cost` visible and correctly stored in Batch details.
- [x] Stock ledger no longer tracks cost (tracked at batch level).
- [x] All related services updated to use batch-level cost.
