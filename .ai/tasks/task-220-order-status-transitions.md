# Task: Order Status Transition Update (REQ-220)

Update order status transitions to allow moving from 'Processing' to 'Cancelled'. 

## 1. Requirement Detail
- **What:** Allow admins to cancel an order even after it has been moved to 'Processing'.
- **How:** Update the `OrderService` transition logic and ensure the UI reflects these options.
- **Data:** Update `getAvailableTransitions` in `OrderService.php`.

## 2. Implementation Steps

### Step 1: Service Layer Update
- Modify `app/Services/OrderService.php`:
    - Update `getAvailableTransitions('Processing')` to include `Cancelled`.
    - Review `updateOrderStatus` for any stock restoration logic. (Stock is only deducted at 'Shipped', so moving from 'Processing' to 'Cancelled' should NOT restore stock if it hasn't been deducted yet).

### Step 2: Verification
- Find a 'Processing' order.
- Verify that 'Cancelled' is now an option in the update status dropdown.
- Update the status to 'Cancelled'.
- Verify the status is updated correctly and logged in history.
- Run `php artisan optimize` to refresh caches.

### Step 3: Documentation
- Update `PROJECT_DOCUMENTATION.md` to reflect the updated transition flow.

## 3. Verification Criteria
- [x] 'Processing' status now shows 'Cancelled' as an available next status.
- [x] Updating to 'Cancelled' from 'Processing' works without errors.
- [x] Stock levels remain unchanged (since deduction only happens at 'Shipped').
- [x] Transition is logged in `order_status_logs`.
