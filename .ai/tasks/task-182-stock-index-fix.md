# Task-182: Stock Index Performance & UX Fixes

## Objective
Fix flickering and blurring issues on the stock index page (`/admin/inventory/inventory-reports/stock`) during scrolling and AJAX updates.

## Implementation Steps
- [x] Optimize `.content-page` background in `resources/views/admin/structure/master.blade.php` by moving heavy radial gradients to a pseudo-element (`::before`) with `pointer-events: none` to reduce repaint costs.
- [x] Add `will-change: transform` to `.content-page .content` to trigger hardware acceleration for smoother scrolling.
- [x] Refactor AJAX loading in `resources/views/admin/inventory/stock/index.blade.php`:
    - Replace `opacity: 0.5` on the entire container with a dedicated `loadingSpinner`.
    - Maintain container height during loading using `min-height` to prevent page jumping.
- [x] Run `php artisan optimize`.

## Verification
- [ ] Scroll through the stock index page and verify that "blurring" or "flickering" is gone.
- [ ] Perform a search or filter and verify the loading state is smooth and doesn't cause content jumping.
