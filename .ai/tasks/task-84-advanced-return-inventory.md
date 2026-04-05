# Task 84: Advanced Return Inventory Processing (REQ-122)

Implement a robust return fulfillment process that allows admins to select specific batches and serial numbers for returned items, while accurately updating stock levels and financial records.

## Requirements
- Support batch and serial selection for returns (similar to the 'Shipped' status UI).
- Handle 'intact' returns:
    - Increment stock in `products`, `product_variants`, `batches`, `batch_products`, and `inventory_levels`.
    - Update `batch_serials` status to `in_stock`.
- Handle 'damaged' returns:
    - Update `batch_serials` status to `damaged` and `stock_status` to `wastage`.
- Update ordered quantities:
    - Decrement quantity in `orders`, `order_items`, and `ordered_product_batches`.
- Log aggregate stock ledger entries.

## Implementation Steps

### 1. Database Layer
- [x] Create migration:
    - Add `batch_id` and `batch_serial_id` to `return_items` table.
- [x] Update `ReturnItem` model:
    - Add `batch_id` and `batch_serial_id` to `$fillable`.
    - Add relationships to `Batch` and `BatchSerial`.

### 2. Service Layer Updates (`ReturnService.php`)
- [x] Update `storeReturnRequest`:
    - Ensure it can handle batch and serial selection if data is passed from the client/admin (though typically selected during approval/receipt).
- [x] Update `updateStatus` (Approved):
    - Allow admins to assign specific batches and serials to returned items.
- [x] Refactor `receiveReturn`:
    - Group items by Batch/Product/Variant for aggregate stock updates.
    - Implement conditional stock increments for 'intact' items across all relevant tables.
    - Implement conditional status updates for 'damaged' items.
    - Implement ordered quantity reductions in `orders`, `order_items`, and `ordered_product_batches`.
    - Log aggregate stock ledger entries.

### 3. UI Layer Updates (`resources/views/admin/returns/show_request.blade.php`)
- [x] Refactor the Return Details/Approval UI:
    - Add batch and serial selection interface similar to the Order Fulfillment UI.
    - Implement AJAX loaders for warehouses and batches associated with the original order.

### 4. Verification
- [x] Run migrations.
- [x] Test 'intact' return: Verify stock levels increment correctly and ordered quantity decrements.
- [x] Test 'damaged' return: Verify serial status updates and wastage tracking.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- [x] Admins can select specific batches/serials for returns.
- [x] 'Intact' returns restore stock across all 5 inventory tables.
- [x] 'Damaged' returns update serial status correctly.
- [x] Ordered quantities in order tables are reduced by the returned amount.
- [x] Stock ledger logs entries accurately.

