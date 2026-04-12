# Task-158: Warehouse Schema Optimization

## Objective
Simplify the inventory structure by removing the `is_quarantine` flag from warehouses and removing the default Quarantine warehouse from the system.

## Implementation Steps
- [x] Create and run migration `drop_is_quarantine_from_warehouses_table`.
- [x] Update `Warehouse.php` model to remove `is_quarantine` from `$fillable`.
- [x] Update `WarehouseSeeder.php` to remove the Quarantine warehouse and the flag.
- [x] Update `WarehouseRequest.php` to remove the validation rule.
- [x] Update controllers (`PurchaseOrderController`, `StockAdjustmentController`, `InventoryReportController`, `WastageController`) to remove filters excluding quarantine warehouses.
- [x] Update `InventoryService.php` to remove filtering and saving logic for `is_quarantine`.
- [x] Update views to remove badges and filters related to quarantine warehouses.
- [x] Manually delete existing 'Quarantine' warehouse and re-run seeder.
- [x] Update tests (`SupplierRmaTest.php`).
- [x] Run `php artisan optimize`.

## Verification
- [x] Warehouse list no longer shows a "Type" filter.
- [x] Creating/Editing a warehouse no longer has a quarantine option.
- [x] 'Quarantine' warehouse is removed from the database.
- [x] All reports and dropdowns show all active warehouses without exclusion logic.
