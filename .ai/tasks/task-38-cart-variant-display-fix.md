# Task 38: Cart & Checkout Variant Display Fix

Fix the product variant display in the Cart, Mini-Cart, and Checkout pages to intelligently handle missing attributes (size/color) and prevent broken UI elements.

## Requirements
- REQ-62: Optimized Variant Display (Intelligent formatting of variant details).

## Steps

### 1. Service Layer Update
- [x] Update `CartService::getCartItems()` to intelligently construct `variant_details`.
- [x] Implement logic to join `size` and `color` only if they exist.
- [x] Fall back to `variant_name` (e.g., "Black - S") if both size and color are null.

### 2. Frontend Implementation (Checkout)
- [x] Update `resources/views/client/checkout/index.blade.php` to display variant details under the product name in the order summary.
- [x] Ensure formatting is clean and consistent with the cart view.

### 3. Verification
- [x] Verify that products with variants show clear details (e.g., "M / Black" or "Black - S") instead of " / ".
- [x] Verify that products without variants still display correctly.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Update `PROJECT_DOCUMENTATION.md` and `requirements.md`.

## Verification Criteria
- Variant info is always readable and professionally formatted.
- No empty slashes or broken strings appear when attributes are missing.
- Checkout summary provides full transparency on selected variants.
