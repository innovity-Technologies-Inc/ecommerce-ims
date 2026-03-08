# Task: Shopping Cart Implementation (REQ-21, REQ-22)

## Status: Completed [x]

## Implementation Plan
1. **Database Setup:**
   - [x] Create `carts` table: `id`, `user_id` (nullable), `session_id` (for guests), `product_id`, `product_variant_id`, `quantity`, `timestamps`.
   - [x] Create `Cart` model with relationships to `User`, `Product`, and `ProductVariant`.

2. **Core Logic (Service Layer):**
   - [x] Create `CartService` to handle hybrid storage logic.
   - [x] Implement `addToCart()` method: Detect auth status and store in DB or session.
   - [x] Implement `getCartItems()` method: Retrieve items for both users and guests.
   - [x] Implement `updateQuantity()` and `removeItem()` methods.
   - [x] Implement `syncCartOnLogin()` logic to move session items to DB upon authentication.

3. **Backend Integration:**
   - [x] Create `CartRequest` for quantity and product validation.
   - [x] Create `CartController` (Thin Controller) to handle routing and AJAX responses.
   - [x] Register web routes for cart operations.

4. **Frontend Integration:**
   - [x] Update Navbar Topbar to include Mini-cart with dynamic counts and content.
   - [x] Update Mobile Navbar for mobile cart access.
   - [x] Create `resources/views/client/cart.blade.php` for the full cart view.
   - [x] Use jQuery AJAX for "Add to Cart" and quantity updates without page refresh.
   - [x] Integrate SweetAlert2/Toastr for user feedback.
- [x] Optimized Cart Page: 8/4 grid layout for banner and grand totals.
- [x] Mobile Responsive Cart: Hybrid Grid/Flex layout (40% image/60% content) with centered elements.

## Verification
- [x] Confirmed hybrid storage logic (Session for guests, DB for users).
- [x] Verified cart item synchronization upon login/registration.
- [x] Confirmed AJAX updates for cart count and total in the header.
- [x] Verified quantity updates and removal in the cart page.
