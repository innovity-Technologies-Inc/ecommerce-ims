# Task 48: Flash Sale Refinement

Refine the Flash Sale module to use dedicated discount fields, ensuring standard discounts are preserved when a flash sale expires. Implement a homepage section with a countdown timer.

## Requirements
- **REQ-73:** Flash Sale Refinement (Dedicated fields and Homepage display).

## Implementation Steps

### 1. Database & Models
- [x] Create migration to add `flash_discount_price` and `flash_discount_percentage` to `products` table.
- [x] Create migration to add `flash_discount_price` and `flash_discount_percentage` to `product_variants` table.
- [x] Update `Product` and `ProductVariant` models to include new fields in `$fillable` and `casts()`.

### 2. Service Layer Refinement
- [x] Update `FlashSaleService::updatePricing` to target `flash_discount_*` fields.
- [x] Update `FlashSaleService::resetProductDiscount` to clear `flash_discount_*` fields instead of standard ones.
- [x] Update `HelperClass::getProductPriceRange` to prioritize `flash_discount_price` if `is_flash_sale` is true.

### 3. Homepage Integration
- [x] Update `FrontendController@home` and `HomepageService` to fetch active Flash Sale and its products.
- [x] Create `resources/views/client/partials/flash_sale.blade.php` with:
    - Flash Sale Title.
    - Countdown Timer (using JS).
    - Product Grid (Product Cards).
    - "View All" button linked to shop page with flash sale filter.
- [x] Include `flash_sale` partial in `resources/views/client/homepage.blade.php`.

### 4. Verification
- [x] Run migrations.
- [x] Update a Flash Sale in Admin and verify products have `flash_discount_*` set and `is_flash_sale` is true.
- [x] Verify standard discounts remain untouched.
- [x] Verify Homepage section appears only when Flash Sale is active.
- [x] Verify Countdown timer works.
- [x] Verify "View All" button filters correctly.
- [x] Test Expiry Automation: Manually set an end date in the past and run `php artisan flash-sale:check-expiry`. Verify flash discounts are cleared but standard ones remain.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- Flash Sale discounts no longer overwrite standard discounts.
- Expired Flash Sales restore products to their previous discounted (or regular) price.
- Homepage displays an attractive Flash Sale section with a functional timer.
