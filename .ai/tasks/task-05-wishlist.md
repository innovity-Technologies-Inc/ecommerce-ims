# Task: Wishlist System (REQ-16, REQ-17)

## Status: Completed [x]

## Implementation Details
1. **Persistent Wishlist (REQ-16):**
   - [x] Created `App\Models\Wishlist` with User and Product associations.
   - [x] Implemented `WishlistService` to manage adding/removing items.
   - [x] Secured all wishlist routes with the `auth:web` guard.

2. **Dynamic Pricing Logic (REQ-17):**
   - [x] Developed logic to calculate the lowest price (Net Price) from base and variant prices.
   - [x] Integrated this logic into `resources/views/client/wishlist.blade.php`.
   - [x] Used `cart_view.blade.php` partial for UI consistency.

## Verification
- [x] Confirmed that only authenticated users can add items to their wishlist.
- [x] Verified that wishlist items are preserved across different sessions.
- [x] Confirmed that the "Net Price" (discounted) is correctly displayed.
