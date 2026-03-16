# Task 46: Global Wishlist Functionality Fix

Fix the wishlist button on the homepage and centralize the wishlist logic to prevent redundancy across the application.

## Requirements
- Ensure the wishlist button on the homepage correctly adds products to the user's wishlist.
- Centralize the `addToWishlist` JavaScript function and the hidden wishlist form to avoid duplication.
- Maintain existing functionality for authenticated users.

## Implementation Steps
1. **Identify Redundancy:** Observed that `product_details.blade.php` and `products.blade.php` both contained identical hidden forms and JS functions for the wishlist.
2. **Centralize Logic:** Moved the hidden wishlist form and the `addToWishlist(productId)` function to `resources/views/client/structure/master.blade.php`.
3. **Clean Up Views:** Removed the redundant code from `resources/views/client/product_details.blade.php` and `resources/views/client/products.blade.php`.
4. **Verification:** Confirmed that `product_card.blade.php` (used on the homepage) correctly calls the now-global function.

## Verification Criteria
- [x] Wishlist button on homepage successfully adds items to the wishlist (for logged-in users).
- [x] Wishlist button on Product Details page still works correctly.
- [x] Wishlist button on Shop (Products) page still works correctly.
- [x] Redundant code removed from specific views.
- [x] `./vendor/bin/pint --dirty` run.
- [x] `php artisan optimize` run.
