# Task 66: Remove Global Low Stock Setting

Remove the system-wide `low_stock_limit` from general settings. Low stock thresholds will now be maintained exclusively at the Product (`min_stock_global`) and InventoryLevel (`min_stock_override`) levels.

## Implementation Steps

1. **Database Migration:**
    - Create a migration to remove the `low_stock_limit` column from the `general_settings` table.

2. **Model Update:**
    - Remove `low_stock_limit` from the `$fillable` array in `App\Models\GeneralSetting`.

3. **Form Request Update:**
    - Remove the validation rule for `low_stock_limit` in `App\Http\Requests\Admin\GeneralSettingRequest`.

4. **Service Update:**
    - Ensure `App\Services\Admin\GeneralSettingService` (or relevant service) no longer handles the `low_stock_limit` field.

5. **Blade View Update:**
    - Remove the "Low Stock Limit" input field from `resources/views/admin/settings/general.blade.php`.

6. **Logic Refinement:**
    - Update `App\Services\DashboardService` to remove dependency on the global `low_stock_limit`.
    - If no specific threshold is set for a product or inventory level, it should default to `0` (meaning no low stock alert) or a hardcoded fallback if preferred by the system, but the prompt says "maintain only from the inventory and product level".

7. **Verification:**
    - Run migrations: `php artisan migrate`.
    - Verify the General Settings page in Admin panel no longer shows the field.
    - Verify the Dashboard "Low Stock" alerts still work based on `min_stock_global` and `min_stock_override`.
    - Run `./vendor/bin/pint --dirty`.
    - Run `php artisan optimize`.

## Verification Criteria

- [x] `general_settings` table no longer has `low_stock_limit` column.
- [x] Admin General Settings form does not contain "Low Stock Limit" field.
- [x] Dashboard low stock calculations use `min_stock_global` (Product) and `min_stock_override` (InventoryLevel) exclusively.
- [x] No regression in dashboard loading or settings saving.
