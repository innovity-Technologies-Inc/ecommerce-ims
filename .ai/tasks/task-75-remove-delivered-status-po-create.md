# Task 75: Remove Delivered Status from PO Creation/Edit

## Requirement
REQ-112: Remove 'Delivered' status option from Purchase Order creation and edit forms to ensure inventory tracking integrity.

## Implementation Details
1.  **Modified `resources/views/admin/inventory/po/create.blade.php`**: Removed `<option value="Delivered">` from the status select to prevent users from setting it directly.
2.  **Modified `resources/views/admin/inventory/po/edit.blade.php`**: Removed `<option value="Delivered">` from the status select.
3.  **Modified `app/Http/Requests/Admin/PurchaseOrderRequest.php`**: Updated the validation rule for `status` to only allow `Draft` and `Sent`.
4.  **Verified Service Logic**: Confirmed that `PurchaseOrderService::updateStatus` already prevents setting `Delivered` manually and that `PurchaseOrderService::receivePurchaseOrder` correctly sets it during the receiving process.

## Verification Criteria
- [x] 'Delivered' option is no longer visible in the status dropdown on the Create PO page.
- [x] 'Delivered' option is no longer visible in the status dropdown on the Edit PO page.
- [x] Attempting to submit 'Delivered' as a status in these forms fails validation.
- [x] PO can still be marked as 'Delivered' through the "Receive PO" form.
- [x] Documentation updated in `PROJECT_DOCUMENTATION.md`.
