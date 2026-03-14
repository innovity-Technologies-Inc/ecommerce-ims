# Task 41: Order Status Finality

Prevent further status changes once an order has been marked as 'Cancelled' or 'Rejected' to ensure data integrity and process finality.

## Requirements
- REQ-65: Order Status Finality (restriction on terminal states).

## Steps

### 1. Service Layer Implementation
- [x] Update `OrderService::updateOrderStatus()` to check the current order status.
- [x] Throw an exception if the current status is `Cancelled` or `Rejected`.
- [x] Remove `Pending` from `OrderService::getStatusList()` to prevent manual reverting.

### 2. Controller Layer Implementation
- [x] Update `OrderController::updateStatus()` with a `try-catch` block.
- [x] Capture service-layer exceptions and return them as session error messages to the admin.

### 3. Frontend Implementation (Admin Panel)
- [x] Update `resources/views/admin/orders/show.blade.php` to conditionally render the status update form.
- [x] Display a descriptive alert message when an order reaches a terminal state instead of the update form.

### 4. Verification
- [x] Verify that an admin can set an order to `Cancelled` or `Rejected`.
- [x] Verify that once set, the update form disappears and is replaced by an alert.
- [x] Verify that manual attempts to bypass the UI (e.g., via API or form tampering) are blocked by the service layer.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Update `PROJECT_DOCUMENTATION.md` and `requirements.md`.

## Verification Criteria
- Terminal statuses are absolute and cannot be reversed by admins.
- The UI provides clear feedback regarding the finality of the order state.
- Backend integrity is maintained via service-level exceptions.
