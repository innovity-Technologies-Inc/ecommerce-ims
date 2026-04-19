# Task-181: PO Currency from General Settings

## Objective
Update the Purchase Order module to use the currency symbol defined in the General Settings instead of handcoded values.

## Implementation Steps
- [x] Fix `.env` file syntax error.
- [x] Revert `PurchaseOrder` model changes (no new column needed).
- [x] Revert `PurchaseOrderService` changes (no persistence needed).
- [x] Rollback and remove migration `add_currency_to_purchase_orders_table`.
- [x] Update PO views to use `\App\HelperClass::generalSettings()->currency ?? '$'`.
    - [x] `resources/views/admin/inventory/po/partials/table.blade.php`
    - [x] `resources/views/admin/inventory/po/show.blade.php`
    - [x] `resources/views/admin/inventory/po/create.blade.php`
    - [x] `resources/views/admin/inventory/po/edit.blade.php`
- [x] Update `resources/views/mail/purchase-order.blade.php`.
- [x] Run `php artisan optimize`.

## Verification
- [x] Verify currency symbols are visible in the PO list, details, and forms.
- [x] Verify currency symbol comes from General Settings (change setting to verify).
- [x] Verify the PO email shows the correct currency from settings.
