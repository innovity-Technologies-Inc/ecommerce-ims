# Task-156: PO Schema Cleanup & Currency Updates

## Objective
Remove the redundant `batch_number` column from `purchase_orders` and add global currency symbols to all amount values in the PO module.

## Implementation Steps
- [x] Create and run migration `drop_batch_number_from_purchase_orders_table`.
- [x] Update `PurchaseOrder.php` model to remove `batch_number` from `$fillable`.
- [x] Update `resources/views/admin/inventory/po/partials/table.blade.php` to include currency symbol.
- [x] Update `resources/views/admin/inventory/po/show.blade.php` to include currency symbol in items table and footer.
- [x] Update `resources/views/admin/inventory/po/create.blade.php` to include currency symbol addons in items table and footer.
- [x] Update `resources/views/admin/inventory/po/edit.blade.php` to include currency symbol addons in items table and footer.
- [x] Update `resources/views/mail/purchase-order.blade.php` to include currency symbol in the table.
- [x] Run `php artisan optimize`.

## Verification
- [x] Verify `purchase_orders` table no longer has `batch_number` column.
- [x] Verify currency symbols are visible in the PO list, details, and forms.
- [x] Verify currency symbols are visible in the PO email sent to suppliers.
