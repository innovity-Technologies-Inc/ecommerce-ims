# Task 81: Order Inventory Selection (Shipped/Delivered Flow)

Connect the client-side order flow with the inventory tracking system. Admins will now select specific warehouses, batches, and serial numbers when marking an order as 'Shipped'.

## 1. Requirement Details
- **REQ-118:** Inventory integration for order fulfillment.
- **Workflow:**
    - **Trigger:** Changing order status to `Shipped`.
    - **UI:** Dynamically show order items. For each item, select:
        - **Warehouse:** Only warehouses containing that specific product/variant.
        - **Batch:** Only batches within that warehouse that have available stock.
        - **Serials:** Select specific `in-stock` and `good` serial numbers from the chosen batch (PO-style tag UI).
    - **Logic:**
        - **Shipped:** Update `batch_serials.stock_status` to `shipped`. Do NOT decrease saleable stock yet.
        - **Delivered:** Update `batch_serials.stock_status` to `sold`. Decrease saleable stock from `warehouses`, `batches`, and `products/variants`.
        - **Stock Ledger:** Create an entry on 'Delivered' status with `unit_cost` and `cost` (quantity * unit_cost, as a negative value).

## 2. Implementation Plan

### Phase 1: Database Updates
- [ ] Create migration for `order_item_serials` table (links `order_item_id` to `batch_serial_id`).
- [ ] Add `OrderItemSerial` model and define relationships in `OrderItem` and `BatchSerial`.

### Phase 2: Backend Logic (Service Layer)
- [ ] Add AJAX endpoints to fetch:
    - [ ] Warehouses containing a specific product/variant.
    - [ ] Batches for a specific product/variant in a chosen warehouse.
    - [ ] Available serials for a specific batch.
- [ ] Update `OrderService::updateOrderStatus`:
    - [ ] Handle `Shipped` logic: Map selected serials to order items and update their status.
    - [ ] Handle `Delivered` logic: Trigger the final inventory deduction and stock ledger logging.
- [ ] Refactor `OrderService::adjustStock` to use the selected batches/serials instead of generic unallocated deduction.

### Phase 3: UI Updates (Admin Panel)
- [ ] Update `resources/views/admin/orders/show.blade.php`:
    - [ ] Modify the status update form to show the "Inventory Allocation" section when 'Shipped' is selected.
    - [ ] Implement the dynamic warehouse/batch/serial selection logic for each order item.
    - [ ] Use Select2 for serial selection (similar to PO receiving).

## 3. Verification Criteria
- [ ] Place an order for products (simple and variant-based).
- [ ] Change status to 'Shipped' and select specific warehouses, batches, and serials.
- [ ] Verify `batch_serials` status is 'shipped'.
- [ ] Change status to 'Delivered'.
- [ ] Verify `batch_serials` status is 'sold'.
- [ ] Verify stock levels are correctly decremented in `products`, `product_variants`, `batches`, and `inventory_levels`.
- [ ] Verify `StockLedger` entry is created with correct financial data.
- [ ] Run `./vendor/bin/pint --dirty`.
- [ ] Run `php artisan optimize`.

## 4. Documentation
- [ ] Update `PROJECT_DOCUMENTATION.md` with the "Order Fulfillment & Inventory Integration" section.
