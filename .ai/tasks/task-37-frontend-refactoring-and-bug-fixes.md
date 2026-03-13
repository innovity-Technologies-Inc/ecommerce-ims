# Task 37: Frontend Refactoring & Bug Fixes

Refactor the Frontend module to follow the Thin Controller/Service Layer pattern and fix multiple frontend issues including price filtering and broken navigation links.

## Requirements
- REQ-59: Advanced Price Filtering (Variant-aware, discount priority).
- REQ-60: Frontend Architectural Refactoring (Thin Controllers, Service Layer, Form Requests).
- REQ-61: Public Invoice Access (Order Tracking invoice printing).

## Steps

### 1. Architectural Refactoring
- [x] Create `FrontendService` to handle shop logic.
- [x] Create `ProductFilterRequest` for shop input validation.
- [x] Create `TrackOrderRequest` for tracking validation (making `order_id` optional for GET access).
- [x] Refactor `FrontendController` to delegate all logic to services.

### 2. Price Filtering Fixes
- [x] Update price filtering to prioritize `discount_price`.
- [x] Implement variant-aware filtering for products with 0/NULL base prices.
- [x] Fix "Class DB not found" error by adding correct imports.

### 3. Navigation & Links Fixes
- [x] Fix broken `#` links in Header (Desktop & Mobile) for Shop and Account menus.
- [x] Fix Category/Subcategory links in the menu to use `category_nav` parameter.
- [x] Ensure "Track Order" link in nav works correctly without failing validation.

### 4. Public Invoice Implementation
- [x] Add `publicInvoice` method to `FrontendController`.
- [x] Register `/order/{order_id}/invoice` route.
- [x] Add "Print Invoice" button to the order tracking results page.

### 5. Cleanup
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Update `PROJECT_DOCUMENTATION.md` and `requirements.md`.

## Verification Criteria
- Shop price filtering works for all product types (simple & variant).
- `FrontendController` is thin and logic resides in `FrontendService`.
- All navigation links in the header are functional.
- Customers can print invoices from the tracking results without login.
