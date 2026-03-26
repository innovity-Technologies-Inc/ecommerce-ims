# Task 63: Purchase Order (PO) Refinement & Stock Ledger Integration

## 1. Requirement
Refine the Purchase Order module with warehouse selection, advanced receiving (batches and serial numbers), quarantine warehouse for damaged goods, and full stock ledger integration.

- **REQ-93:** PO Warehouse Selection.
- **REQ-94:** PO Receiving Refinement (Batch IDs, Serial Numbers).
- **REQ-95:** Quarantine Warehouse (`is_quarantine` flag).
- **REQ-96:** Stock Ledger Integration.

## 2. Implementation Steps

### 1. Database Layer
- **Warehouses:** Add `is_quarantine` (boolean, default 0) to `warehouses` table.
- **Purchase Orders:** Re-add `warehouse_id` to `purchase_orders`.
- **Purchase Order Items:**
    - Remove `damaged_quantity`, `missing_quantity`.
    - Add `subtotal` (decimal).
- **Batches:** Create `batches` table:
    - `id`, `batch_number`, `purchase_order_id`, `product_id`, `product_variant_id`, `warehouse_id`, `quantity`, `expiry_date` (optional but good), `created_at`, `updated_at`.
- **Batch Serials:** Create `batch_serials` table:
    - `id`, `batch_id`, `warehouse_id`, `product_id`, `product_variant_id`, `serial_no`, `status` (e.g., 'Available', 'Sold', 'Damaged').
- **Stock Ledger:** Ensure `stock_ledgers` table has proper fields for transaction tracking.

### 2. Models & Seeders
- Update `Warehouse`, `PurchaseOrder`, `PurchaseOrderItem` models.
- Create `Batch`, `BatchSerial` models.
- Update `DatabaseSeeder` or create/update `WarehouseSeeder` to include a "Quarantine" warehouse with `is_quarantine = 1`.

### 3. Business Logic (Service Layer)
- **PurchaseOrderService:**
    - Update `storePurchaseOrder` to handle `warehouse_id`.
    - Implement `receivePurchaseOrder(PurchaseOrder $po, array $data)`:
        - Logic to handle received quantities and damaged quantities.
        - Create `Batch` records for received items.
        - Create `BatchSerial` records if serials are provided.
        - Move damaged items to the "Quarantine" warehouse.
        - Update `PurchaseOrderItem` received quantities.
        - Update total stock in `InventoryLevel`, `Product`, and `ProductVariant`.
        - **Stock Ledger:** Record every stock movement (Received, Damaged/Quarantined) in `stock_ledgers`.

### 4. Validation (Form Requests)
- Update `PurchaseOrderRequest` for warehouse selection.
- Create `PurchaseOrderReceiveRequest` for the refinement.

### 5. Frontend (Admin Panel)
- **PO Create/Edit:** Add Warehouse dropdown.
- **PO Receive Form:**
    - Add fields for Batch Number and Serial Numbers (Tag-style input using a JS plugin).
    - Add fields for Damaged Quantity.
- **Stock Ledger View:** (If not already present) Create a view to see the history of stock movements.

### 6. Finalization
- Run `./vendor/bin/pint --dirty`.
- Run `php artisan optimize`.
- Update `PROJECT_DOCUMENTATION.md`.

## 3. Verification Criteria
- [ ] PO creation includes warehouse selection.
- [ ] Receiving PO creates Batch and BatchSerial records.
- [ ] Damaged items are correctly moved to the "Quarantine" warehouse.
- [ ] Stock Ledger correctly records all transactions with reason codes.
- [ ] Product and Variant total stocks are updated consistently.
- [ ] Tag-style serial number input works as expected.
