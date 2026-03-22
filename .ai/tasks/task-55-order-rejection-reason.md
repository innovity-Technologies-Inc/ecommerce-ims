# Task 55: Order Cancellation/Rejection Remarks

Implement a system to capture and display remarks/reasons when an order is cancelled or rejected by an administrator.

## 1. Database Implementation
- [x] Create a migration to add `rejection_reason` (text, nullable) to the `orders` table.
- [x] Update the `Order` model to include `rejection_reason` in `$fillable`.

## 2. Backend Implementation (Service & Validation)
- [x] Update `UpdateOrderStatusRequest` to include `rejection_reason` validation.
    - It should be required if `order_status` is 'Cancelled' or 'Rejected'.
- [x] Update `OrderService::updateOrderStatus()` to save the `rejection_reason`.
- [x] Update `OrderStatusUpdateMail` to accept and display the `rejection_reason` in the email template.

## 3. Admin-Side Implementation
- [x] Update `admin.orders.show` view:
    - Add a textarea for "Reason/Remarks" in the status update form.
    - Use JavaScript to show/hide this textarea based on the selected status (show only for 'Cancelled' or 'Rejected').
    - Display the saved reason in the order details or history section if it exists.

## 4. Client-Side Implementation
- [x] Update `client.track-order` view to show the rejection/cancellation reason if the order is in that status.
- [x] Update `client.account.order-details` view to show the reason.

## 5. Finalization
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## 6. Verification Criteria
- [x] Admin must provide a reason when cancelling or rejecting an order.
- [x] Reason is saved correctly in the database.
- [x] Customer receives an email with the reason if "Email Notify" is checked.
- [x] Customer can see the reason on the tracking page.
- [x] Customer can see the reason in their account order details page.
