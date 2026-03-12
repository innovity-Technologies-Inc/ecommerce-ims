# Task: Advanced Order Filtering

Implement advanced filters for the Order Index page in the Admin Panel.

## Implementation Steps

1.  **Service Layer**: Update `OrderService::getAllOrders()` to handle:
    -   `order_status`
    -   `payment_method`
    -   `payment_status`
    -   `date_from` and `date_to`
2.  **Controller**: Ensure `OrderController::index()` passes all request parameters to the service.
3.  **View (Index)**:
    -   Add dropdowns for Order Status, Payment Method, and Payment Status.
    -   Add date inputs for From and To dates.
    -   Update AJAX script to include these new parameters.
4.  **Helper Class**: Add a method to `HelperClass` to get unique values for payment methods and statuses if needed, or define them in the Service.

## Verification Criteria
- [x] Orders can be filtered by Order Status.
- [x] Orders can be filtered by Payment Method.
- [x] Orders can be filtered by Payment Status.
- [x] Orders can be filtered by a Date Range.
- [x] Filters work via AJAX without page refresh.
- [x] Pagination preserves all active filters.
