# Task-159: Sales Report Status Fix

## Objective
Ensure that the Sales Report only calculates data from orders that have been successfully 'Delivered'.

## Implementation Steps
- [x] Update `app/Services/ReportService.php`.
- [x] Modify `applyOrderFilters` to hardcode `$query->where('order_status', 'Delivered')`.
- [x] Remove the dynamic `order_status` check from the service logic.
- [x] Update `resources/views/admin/reports/sales.blade.php`.
- [x] Remove the "Order Status" `<select>` field from the filter form to prevent users from trying to filter for other statuses in a sales-only report.
- [x] Run `php artisan optimize`.

## Verification
- [x] Open the Sales Report dashboard.
- [x] Verify that the "Order Status" filter is no longer visible.
- [x] Verify that the totals (Net Sales, Units Sold, etc.) only include orders marked as 'Delivered'.
- [x] Apply other filters (date, warehouse) and ensure they still work correctly while maintaining the 'Delivered' constraint.
