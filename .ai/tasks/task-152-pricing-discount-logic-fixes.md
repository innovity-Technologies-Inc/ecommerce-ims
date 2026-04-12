# Task-152: Pricing & Discount Logic Fixes

## Objective
Fix pricing and discount calculation issues for products with variants using global pricing, and ensure regular prices are displayed beside discounted prices in cart and checkout.

## Implementation Steps
- [x] Refactor `CartService::getCartItems()` to implement proper regular price and discount fallbacks.
- [x] Refactor `HelperClass::getProductPriceRange()` to ensure consistency with cart logic.
- [x] Update `resources/views/client/cart.blade.php` to display regular prices.
- [x] Update `resources/views/client/checkout/index.blade.php` to display regular prices in the order summary.
- [x] Update `resources/views/client/structure/mini-cart.blade.php` to display regular prices.
- [x] Run `php artisan optimize`.

## Verification
- [x] Add a variant product with global price/discount to cart.
- [x] Verify subtotal is calculated correctly using the global prices.
- [x] Verify regular price is visible with a line-through in the cart.
- [x] Verify the same in checkout and mini-cart.
