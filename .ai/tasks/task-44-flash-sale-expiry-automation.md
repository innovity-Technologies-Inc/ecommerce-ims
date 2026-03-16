# Task 44: Flash Sale Expiry Automation

Automatically reset product discounts to 0 when a flash sale reaches its end date.

## Requirements
- Add an `isActive()` helper to the `FlashSale` model to check status and expiry.
- Update `FlashSaleService` to respect the `end_date` during synchronization.
- Create an Artisan command `flash-sale:check-expiry` to automate the reset.
- Schedule the command to run every minute.
- Add a web route `/check-flash-sale-expiry` to manually trigger the scheduler via URL.
- Document cPanel Cron Job setup (both CLI and Web methods).

## Implementation Steps
1. **Model Update:** Added `isActive()` to `App\Models\FlashSale`.
2. **Service Update:** Modified `FlashSaleService::syncAllDiscounts()` to use the new activity check.
3. **Console Command:** Registered `flash-sale:check-expiry` in `routes/console.php`.
4. **Scheduling:** Added `Schedule::command('flash-sale:check-expiry')->everyMinute()`.
5. **Web Route:** Added `/check-flash-sale-expiry` to `routes/web.php`.
6. **Documentation:** Updated `PROJECT_DOCUMENTATION.md` with technical details and cPanel instructions.

## Verification Criteria
- [x] Setting a past `end_date` in Admin Panel and saving resets prices to 0.
- [x] Command `php artisan flash-sale:check-expiry` manually resets prices for expired sales.
- [x] Logic handles both status toggle and clock-based expiry.
- [x] Web route `/check-flash-sale-expiry` successfully triggers the scheduler.
- [x] `./vendor/bin/pint --dirty` is run.
- [x] `php artisan optimize` is run.
