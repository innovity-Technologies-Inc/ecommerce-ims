# Task: Fix Undefined array key "closing_stock" in Warehouse Performance Report (REQ-93)

## Objective
The warehouse performance report index page fails with an `ErrorException: Undefined array key "closing_stock"` on line 67.

## Implementation Details
1.  Verify the exact key being returned by `WarehousePerformanceService::getPerformanceReport`.
2.  Update `resources/views/admin/reports/warehouse-performance/index.blade.php` to use the correct key.
3.  Update `resources/views/admin/reports/warehouse-performance/show.blade.php` if necessary.
4.  Standardize the key as `total_closing_stock` in both service and views to avoid confusion.
5.  Clear Laravel view and optimization caches.

## Verification Criteria
- [x] Accessible `/admin/reports/warehouse-performance` without error.
- [x] Accessible `/admin/reports/warehouse-performance/{id}` without error.
- [x] Correct closing stock value displayed in both views.
- [x] PHPUnit tests pass if relevant.
- [x] Run `php artisan optimize` and `vendor/bin/pint --dirty`.
