# Task 47: Wishlist Add-to-Cart Functionality Fix

Fix the "Add to Cart" button on the wishlist page to ensure it correctly triggers the cart addition or redirects to product options.

## Requirements
- The "Add to Cart" button in the wishlist table must be functional.
- For products with variants, it should redirect to the product details page (Select Options).
- For discontinued products, it should redirect to the product details page (View Details).
- For simple products, it should trigger the global AJAX add-to-cart handler.

## Implementation Steps
1. **Identify Logic Gap:** Observed that the "Add to Cart" button in `resources/views/client/partials/cart_view.blade.php` was a static placeholder link (`href="#"`).
2. **Apply Dynamic Logic:** Updated the template to check for product status and variants, matching the logic used in `product_card.blade.php`.
3. **Integration:** Added the necessary data attributes (`data-product-id`) and classes (`add-to-cart-btn`) to trigger the global jQuery handler defined in `cart-scripts.blade.php`.

## Verification Criteria
- [x] "Add to Cart" button for a simple product triggers AJAX and shows success notification.
- [x] Button changes to "Select Options" for products with variants.
- [x] Button changes to "View Details" for discontinued products.
- [x] ./vendor/bin/pint --dirty run.
- [x] php artisan optimize run.
