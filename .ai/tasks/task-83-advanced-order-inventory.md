# Task 83: Advanced Order Inventory Processing (REQ-121)

Overhaul the order fulfillment process to support multi-batch allocation per order item, track procurement costs, and automate aggregate stock ledger entries.

## Requirements
- Create `ordered_product_batches` table to track detail allocation.
- Add `total_cost` to `orders` and `order_items`.
- Support multiple batches for a single order item in 'Shipped' status.
- Fetch unit costs from `batch_products` and calculate total procurement cost.
- Log aggregate stock ledger entries per batch.

## Implementation Steps

### 1. Database Layer
- [x] Create migration:
    - New table `ordered_product_batches`: `id`, `order_id`, `order_item_id`, `product_id`, `product_variant_id` (null), `batch_id`, `warehouse_id`, `quantity`, `unit_cost`, `subtotal_cost`, `timestamps`.
    - Update `orders`: add `total_cost` (decimal 15,2).
    - Update `order_items`: add `total_cost` (decimal 15,2).
- [x] Create `OrderedProductBatch` model.
- [x] Update `Order` and `OrderItem` models with relationships and casts.

### 2. Service Layer Updates (`OrderService.php`)
- [x] Update `updateOrderStatus` for 'Shipped' status:
    - Handle nested allocation data from UI.
    - Validate total quantity per item.
    - Save records to `ordered_product_batches`.
    - Calculate and save costs.
- [x] Update `updateOrderStatus` for 'Delivered' status:
    - Loop through `ordered_product_batches` for stock deduction.
    - Log aggregate stock change per batch.

### 3. UI Layer Updates (`resources/views/admin/orders/show.blade.php`)
- [x] Refactor "Inventory Allocation" section:
    - Use dynamic row addition for batches per item.
    - Implement JS for adding/removing batch rows.
    - Implement JS validation for quantity sums.
    - Update serial selection to be batch-row specific.
- [x] Update order summary to show "Total Procurement Cost".

### 4. Verification
- [x] Run migrations.
- [x] Test multi-batch fulfillment.
- [x] Verify cost calculations in DB.
- [x] Verify stock levels after delivery.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- [x] Multiple batches can be selected for a single order item.
- [x] `total_cost` correctly reflects the sum of procurement costs from selected batches.
- [x] Stock levels are decremented accurately from the specific batches/warehouses chosen.
- [x] Stock ledger has aggregate entries (no split serial rows).
