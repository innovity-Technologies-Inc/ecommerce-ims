# Task 65: Inventory Data Refinement

## 1. Requirement
Refine the data structure for inventory tracking to support financial reporting and improved product lifecycle management.
- **Batches:** Add `supplier_id` to the `batches` header.
- **Batch Serials:** Update status options to 'in-stock' and 'sold'.
- **Stock Ledger:** Add `supplier_id`, `unit_cost`, and `cost` (total change in value).

- **REQ-99:** Inventory Data Refinement.

## 2. Implementation Steps

### 1. Database Layer (Migrations)
- **Batches:** Add `supplier_id` (nullable, constrained to `suppliers`).
- **Batch Serials:**
    - Update `status` enum to include `in-stock` and `sold`.
    - Set default value to `in-stock`.
- **Stock Ledgers:**
    - Add `supplier_id` (nullable, constrained to `suppliers`).
    - Add `unit_cost` (decimal).
    - Add `cost` (decimal, can be negative).

### 2. Models
- Update `Batch`, `BatchSerial`, and `StockLedger` models with new fillable fields and relationships.

### 3. Service Layer
- **PurchaseOrderService:** Update `receivePurchaseOrder` to:
    - Pass `supplier_id` when creating batches.
    - Set `batch_serials` status to `in-stock`.
    - Pass `supplier_id`, `unit_cost`, and calculated `cost` when logging stock changes.
- **InventoryService:** Update `logStockChange` signature and implementation to handle the new fields.

### 4. Finalization
- Run `./vendor/bin/pint --dirty`.
- Run `php artisan optimize`.
- Update `PROJECT_DOCUMENTATION.md`.

## 3. Verification Criteria
- [ ] Batches table contains `supplier_id`.
- [ ] Batch Serials table uses `in-stock` and `sold` statuses.
- [ ] Stock Ledger records `unit_cost` and total `cost` for every transaction.
- [ ] PO receipt correctly populates all new fields in batches and ledgers.
