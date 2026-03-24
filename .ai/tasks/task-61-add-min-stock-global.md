# Task 61: Add `min_stock_global` to `products` Table

## Requirements
- **REQ-87:** Global Minimum Stock: Add `min_stock_global` field to `products` table with a default value of `0` to track low stock alerts at the product level.

## Implementation Steps

### 1. Database Migration
- [x] Create a migration to add `min_stock_global` column to `products` table.
- [x] Run the migration.

### 2. Model Update
- [x] Update `Product` model's `$fillable` array to include `min_stock_global`.
- [x] Add cast for `min_stock_global` to `integer`.

### 3. Service Layer Update
- [x] Update `ProductService` to handle `min_stock_global` during store and update operations.

### 4. Admin View Update
- [x] Update Product Create/Edit forms to include an input for `min_stock_global`.

### 5. Verification
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- `min_stock_global` column exists in the `products` table with a default value of `0`.
- Admin can set and update the global minimum stock for a product.
- Logic is maintained in the Service layer.
- Views follow Bootstrap 5 standards.
