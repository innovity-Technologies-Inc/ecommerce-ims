# Task-168: Sidebar Active State Logic

## Objective
Implement dynamic `active` and `show` classes in the admin sidebar to highlight the current menu item and keep collapsible sections open when related sub-routes are active.

## Implementation Steps
- [x] Update `resources/views/admin/structure/partials/sidebar.blade.php` with `Request::routeIs()` logic.
- [x] Apply `active` class to top-level links (Dashboard, Categories, Brands, Products, etc.).
- [x] Apply `active` class to sub-menu links (Return Requests, Returned Products, Coupons, Flash Sale, etc.).
- [x] Apply `show` and `aria-expanded="true"` to collapsible sections (Returns, Promotions, Homepage, Inventory Reports, Management, Settings).
- [x] Handle special cases like homepage sections using `Request::is()` for parameter-based matching.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- Active menu item is highlighted.
- Collapsible menus stay open when one of their child pages is active.
- Sub-items are highlighted when their specific route is active.
- Correct handling of wildcard routes (e.g., `admin.products.*` highlights Products for edit/create/show pages).
