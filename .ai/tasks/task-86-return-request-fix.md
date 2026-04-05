# Task 86: Return Request UI & Logic Fix

Fix the return request approval module in the admin panel by expanding the form width and ensuring correct batch/serial data filtering.

## Requirements
- **Full-Width Form:** The approval form in `admin.returns.show_request` should be full width (col-12) instead of a sidebar (col-4).
- **Accurate Batch/Serial Selection:** The batch and serial number selection during approval must only include batches and serials that were actually shipped for that specific order.

## Implementation Plan

### 1. UI Refinement (Blade)
- **File:** `resources/views/admin/returns/show_request.blade.php`
- **Change:** 
    - Move the "Action" card out of the `col-lg-4` sidebar.
    - Restructure the layout to use `col-lg-12` for the return details and `col-lg-12` for the approval form when status is 'pending'.
    - Keep "Customer & Order Details" in a sidebar or move it to a more appropriate place (e.g., top or side).
    - Update the return item card in the allocation section to use better spacing now that it has more width.

### 2. Logic Verification (Service)
- **File:** `app/Services/ReturnService.php`
- **Verification:** 
    - Ensure `getOrderBatches(int $orderItemId)` correctly filters by `order_item_id` in `ordered_product_batches`.
    - Ensure `getOrderSerials(int $orderItemId, int $batchId)` correctly filters by `order_item_id` and `batch_id` in `batch_serials` with status 'shipped'.
- **Fix:** Update the `data-order-item-id` in the Blade file to correctly handle cases where `product_variant_id` might be null, ensuring it matches the correct `order_items` record.

## Verification Criteria
- [x] View the return request details page in the admin panel.
- [x] Verify that the approval form is now full width.
- [x] Test the batch selection dropdown for a return item. It should only show batches that were shipped for that order item.
- [x] Test the serial selection modal. It should only show serials that were shipped for that order item and selected batch.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.
