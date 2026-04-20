# Task 218: Cart Empty State and Button Standardization

Streamline the empty cart state and standardize the size of action buttons.

## Requirement Reference
- **REQ-218:** Cart Empty State and Button Standardization.

## Implementation Steps

### 1. View Updates (`resources/views/client/cart.blade.php`)
- [ ] **CSS Update:** Add a style rule to standardize button sizes and keep the empty cart text on one line.
    ```css
    .cart-shiping-update a,
    .cart-clear button,
    .cart-clear a {
        min-width: 220px;
        text-align: center;
        display: inline-flex !important;
        justify-content: center;
        align-items: center;
    }
    ```
- [ ] **Empty Cart Text:** Add `white-space: nowrap;` to the "Your cart is empty." header.
- [ ] **Remove Button:** Delete the "Go to Shop" button from the empty cart table row.
- [ ] **Conditional Logic:** Wrap the `.cart-clear` div and the Banner/Totals row in `@if($cartItems->count() > 0)` blocks.

### 2. Verification
- [ ] Load cart with items: Verify buttons are uniform and sidebars are visible.
- [ ] Load cart without items: Verify "Your cart is empty." is on one line, and ONLY "Continue Shopping" is visible at the bottom.

## Documentation Update
- [ ] Note the UI standardization for cart action buttons in `PROJECT_DOCUMENTATION.md`.
