# Task 12: Cart Module Architectural Refactoring

Refactor the Cart module to strictly follow the "Service Layer", "Thin Controller", and "Form Request" patterns.

## 1. Requirement
- **REQ-27:** Cart Module Architectural Refactoring (Ensure full adherence to Service Layer and Form Request patterns).

## 2. Implementation Steps
- [x] **Form Requests**:
    - Create `UpdateCartRequest` for quantity updates.
    - Create `RemoveCartItemRequest` for item removal.
- [x] **CartService**:
    - Add `getCartTotal()` method.
    - Add `getItemSubtotal(int $cartId)` method.
- [x] **CartController**:
    - Refactor `updateQuantity()` to use `UpdateCartRequest`.
    - Refactor `removeItem()` to use `RemoveCartItemRequest`.
    - Ensure all response logic is concise and relies only on the Service.
- [x] **Verification**:
    - Verify AJAX updates in the cart page still work correctly.

## 3. Verification Criteria
- [x] Manual verification of Cart page (Add, Update, Remove).
- [x] Ensure no business logic remains in `CartController`.
