# Task 16: Checkout System & Order Management

Implementation of the checkout process, order creation, and admin order management.

## 1. Requirement Logging
- [x] **REQ-32:** Checkout System & Order Management.

## 2. Implementation Steps

### Phase 1: Database & Models
- [x] Create migration for `orders` table.
- [x] Create migration for `order_items` table.
- [x] Create `Order` model with relationships and `casts()`.
- [x] Create `OrderItem` model with relationships.

### Phase 2: Checkout Frontend
- [x] Create `CheckoutController` for frontend checkout.
- [x] Create `CheckoutRequest` for form validation.
- [x] Create `resources/views/client/checkout/index.blade.php`.
- [x] Implement pre-filling of form for authenticated users.
- [x] Implement payment method selection (COD as default).
- [x] Implement checkout redirect from Cart page.

### Phase 3: Order Processing (Service Layer)
- [x] Create `OrderService` to handle order creation and status management.
- [x] Implement `placeOrder` logic in `OrderService`.
- [x] Implement unique `order_id` generation.
- [x] Implement cart clearing after successful order.
- [x] Create `OrderConfirmationMail` mailable.
- [x] Send order confirmation email to the customer.

### Phase 4: Admin Order Management
- [x] Create `AdminOrderController` (Implemented as `OrderController`).
- [x] Create Admin order list view.
- [x] Create Admin order details view.
- [x] Implement status update logic in `OrderService`.
- [x] Implement Reject/Delete order logic.
- [x] Create `OrderStatusUpdateMail` mailable.
- [x] Implement "Email Notify" checkbox for status changes.

### Phase 5: Verification & Styling
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Create and run PHPUnit tests for checkout and order management.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## 3. Verification Criteria
- [x] Redirect from Cart to Checkout works correctly.
- [x] Checkout form pre-fills for logged-in users.
- [x] Form validation works for guest users (matches registration fields).
- [x] Order is correctly saved in `orders` and `order_items` tables.
- [x] Cart is cleared after checkout.
- [x] Customer receives an email after placing an order.
- [x] Admin can view, update, and delete orders.
- [x] Status updates trigger emails only when "Email Notify" is checked.
- [x] Unique `order_id` is generated for every order.
