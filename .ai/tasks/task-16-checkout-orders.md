# Task 16: Checkout System & Order Management

Implementation of the checkout process, order creation, and admin order management.

## 1. Requirement Logging
- [x] **REQ-32:** Checkout System & Order Management.

## 2. Implementation Steps

### Phase 1: Database & Models
- [ ] Create migration for `orders` table.
- [ ] Create migration for `order_items` table.
- [ ] Create `Order` model with relationships and `casts()`.
- [ ] Create `OrderItem` model with relationships.

### Phase 2: Checkout Frontend
- [ ] Create `CheckoutController` for frontend checkout.
- [ ] Create `CheckoutRequest` for form validation.
- [ ] Create `resources/views/client/checkout/index.blade.php`.
- [ ] Implement pre-filling of form for authenticated users.
- [ ] Implement payment method selection (COD as default).
- [ ] Implement checkout redirect from Cart page.

### Phase 3: Order Processing (Service Layer)
- [ ] Create `OrderService` to handle order creation and status management.
- [ ] Implement `placeOrder` logic in `OrderService`.
- [ ] Implement unique `order_id` generation.
- [ ] Implement cart clearing after successful order.
- [ ] Create `OrderConfirmationMail` mailable.
- [ ] Send order confirmation email to the customer.

### Phase 4: Admin Order Management
- [ ] Create `AdminOrderController`.
- [ ] Create Admin order list view.
- [ ] Create Admin order details view.
- [ ] Implement status update logic in `OrderService`.
- [ ] Implement Reject/Delete order logic.
- [ ] Create `OrderStatusUpdateMail` mailable.
- [ ] Implement "Email Notify" checkbox for status changes.

### Phase 5: Verification & Styling
- [ ] Run `./vendor/bin/pint --dirty`.
- [ ] Create and run PHPUnit tests for checkout and order management.
- [ ] Update `PROJECT_DOCUMENTATION.md`.

## 3. Verification Criteria
- [ ] Redirect from Cart to Checkout works correctly.
- [ ] Checkout form pre-fills for logged-in users.
- [ ] Form validation works for guest users (matches registration fields).
- [ ] Order is correctly saved in `orders` and `order_items` tables.
- [ ] Cart is cleared after checkout.
- [ ] Customer receives an email after placing an order.
- [ ] Admin can view, update, and delete orders.
- [ ] Status updates trigger emails only when "Email Notify" is checked.
- [ ] Unique `order_id` is generated for every order.
