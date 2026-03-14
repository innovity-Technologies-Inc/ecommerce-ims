# Task 40: Automated Stock Management

Implement automatic stock deduction upon order placement and restorative increment upon order cancellation or rejection.

## Requirements
- REQ-64: Automated Stock Management (lifecycle synchronization).

## Steps

### 1. Service Layer Implementation
- [x] Update `OrderService::placeOrder()` to decrement stock for each item (product or variant) during the transaction.
- [x] Implement `OrderService::adjustStock()` helper to handle bulk stock changes.
- [x] Update `OrderService::updateOrderStatus()` to trigger stock restoration when moving to `Cancelled` or `Rejected`.
- [x] Ensure bidirectional status changes (Active <-> Restorative) handle stock correctly.

### 2. Verification
- [x] Verify that placing an order reduces the available stock.
- [x] Verify that cancelling/rejecting an order returns the stock to inventory.
- [x] Ensure database transactions maintain data integrity.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Update `PROJECT_DOCUMENTATION.md` and `requirements.md`.

## Verification Criteria
- Inventory levels are always accurate relative to order volume and status.
- Product variants are prioritized for stock management when applicable.
- No race conditions or data loss during concurrent orders.
