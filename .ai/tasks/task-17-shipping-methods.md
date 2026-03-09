# Task 17: Shipping Method Module & Cart Integration

Implementation of Admin Shipping Method CRUD and its integration into the Cart/Checkout flow.

## 1. Requirement Logging
- [x] **REQ-33:** Shipping Method Module & UI Integration.

## 2. Implementation Steps

### Phase 1: Database & Models
- [ ] Create migration for `shipping_methods` table (`name`, `price`, `short_description`, `status`).
- [ ] Create `ShippingMethod` model.
- [ ] Update `carts` table to store `shipping_method_id` (optional, can also use session).
- [ ] Update `orders` table to store `shipping_method_id` or just the name/price.

### Phase 2: Admin Shipping Method CRUD
- [ ] Create `ShippingMethodController`.
- [ ] Create `ShippingMethodService`.
- [ ] Create `ShippingMethodRequest`.
- [ ] Create Admin views for List, Create, and Edit.
- [ ] Add Shipping Methods to Admin sidebar.

### Phase 3: Cart Page Integration
- [ ] Fetch active shipping methods in `CartController`.
- [ ] Display shipping methods in `resources/views/client/cart.blade.php`.
- [ ] Implement AJAX/Session logic to select shipping method and update `Grand Total`.
- [ ] Show `short_description` under each option in the cart.

### Phase 4: Checkout Page Integration
- [ ] Pass selected shipping method to `CheckoutController`.
- [ ] Display selected shipping method in `resources/views/client/checkout/index.blade.php`.
- [ ] Ensure `OrderService` saves the correct shipping charge and method name.

### Phase 5: Verification & Styling
- [ ] Run `./vendor/bin/pint --dirty`.
- [ ] Verify using existing seeders (create a `ShippingMethodSeeder` if needed).
- [ ] Update `PROJECT_DOCUMENTATION.md`.

## 3. Verification Criteria
- [ ] Admin can Create, Read, Update, and Delete shipping methods.
- [ ] Cart page shows all active shipping methods with names, prices, and short descriptions.
- [ ] Selecting a shipping method updates the `Grand Total` on the cart page.
- [ ] Selected shipping method is correctly displayed on the checkout page.
- [ ] Placing an order saves the shipping method and charge correctly.
