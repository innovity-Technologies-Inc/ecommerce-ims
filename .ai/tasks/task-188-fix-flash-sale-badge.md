# Task: 188 - Fix Flash Sale Discount Badge

## Requirement
The flash sale discount badge is not showing even when a flash sale is active.

## Objectives
1.  Add `is_flash_sale` to the `Product` model's `$fillable` array to allow updates via the `FlashSaleService`.
2.  Add `is_flash_sale` to the `Product` model's `casts()` method for correct boolean handling.
3.  Fix existing data for products currently in an active flash sale.

## Implementation Steps

### 1. Update Product Model (REQ-188)
- Update `app/Models/Product.php` to include `is_flash_sale` in `$fillable` and `casts()`.

### 2. Correct Existing Data
- Manually update `is_flash_sale = 1` for products linked to active flash sales.

## Verification Criteria
- [x] Verified that `is_flash_sale` is now fillable in the model.
- [x] Verified that active flash sale products now have `is_flash_sale = 1` in the database.
- [x] Verified (theoretical) that `getProductPriceRange` now correctly identifies the discount.
