# Task 19: Order History & Tracking

Implement client-side order history for authenticated users and order tracking for guests.

## 1. Requirement Logging
- [x] **REQ-35:** Order History & Tracking (Authenticated user history and guest order tracking by ID).

## 2. Implementation Steps

### Phase 1: Logic (Service Layer)
- [x] Add `getUserOrders` to `OrderService`.
- [x] Add `trackOrderById` to `OrderService`.
- [x] Ensure `trackOrderById` handles both authenticated and guest orders securely.

### Phase 2: Controllers & Routing
- [x] Add `orderHistory` method to `CustomerController`.
- [x] Add `trackOrder` (GET/POST) to `FrontendController`.
- [x] Create `TrackOrderRequest` for validation.
- [x] Register routes in `web.php`.

### Phase 3: Views (Frontend)
- [x] Create `resources/views/client/account/orders.blade.php` for user order history.
- [x] Create `resources/views/client/account/order-details.blade.php` for specific order info.
- [x] Create `resources/views/client/track-order.blade.php` with tracking form and status display.
- [x] Use Bootstrap 5 progress bars or similar for tracking visualization.

### Phase 4: Verification & Styling
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Verify history with an authenticated user (using seeders).
- [x] Verify tracking with multiple order IDs.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## 3. Verification Criteria
- [x] Authenticated users can see a list of their past orders in "My Account".
- [x] Clicking on an order shows full details (items, prices, shipping, status).
- [x] Guests can track any order by inputting its unique `order_id` (e.g., ORD-XXXXXXXXXX).
- [x] Clear status indication (Pending, Processing, Delivered, etc.).
