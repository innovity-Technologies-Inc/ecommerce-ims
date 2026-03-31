# Task 71: Stock Adjustment Module

Implement a manual stock entry system to adjust inventory without requiring a Purchase Order.

## 1. Database Schema

### `stock_adjustments` Table (Header)
- `id` (Primary Key)
- `adjustment_number` (Unique string, e.g., ADJ-20260331-001)
- `warehouse_id` (Foreign Key to `warehouses`)
- `batch_id` (Foreign Key to `batches`)
- `adjustment_date` (Date)
- `remarks` (Text, Nullable)
- `created_by` (Foreign Key to `admins`)
- `created_at`, `updated_at`

### `stock_adjustment_items` Table (Line Items)
- `id` (Primary Key)
- `stock_adjustment_id` (Foreign Key to `stock_adjustments`)
- `product_id` (Foreign Key to `products`)
- `product_variant_id` (Nullable Foreign Key to `product_variants`)
- `quantity` (Integer)
- `unit_cost` (Decimal)
- `created_at`, `updated_at`

## 2. Implementation Steps

### Step 1: Backend Setup (Migrations & Models)
- [ ] Create migration for `stock_adjustments` and `stock_adjustment_items`.
- [ ] Create models: `StockAdjustment`, `StockAdjustmentItem`.
- [ ] Define relationships in `Warehouse`, `Batch`, `Product`, `ProductVariant`, `Admin`.

### Step 2: Service Layer & Form Requests
- [ ] Create `StockAdjustmentService` to handle complex multi-table logic.
- [ ] Create `StockAdjustmentRequest` for validation.

### Step 3: Admin Controller & UI
- [ ] Create `StockAdjustmentController`.
- [ ] **Index Page:**
    - [ ] List all adjustments with FlexSearch (filters: warehouse, date).
- [ ] **Create Page:**
    - [ ] Select Warehouse.
    - [ ] Enter/Select Batch Number.
    - [ ] Dynamic Product/Variant selection.
    - [ ] Quantity, Unit Cost inputs.
    - [ ] Serial Number tagging (if applicable).
- [ ] **Show Page:**
    - [ ] Detailed view of adjustment and its impact.

### Step 4: Logic Integration (Inventory Synchronization)
- [ ] **On Creation:**
    - [ ] Create or Update `Batch` (supplier_id will be null).
    - [ ] Create `BatchProduct` records.
    - [ ] Create `BatchSerial` records (stock_status = 'in_stock', product_status = 'good').
    - [ ] Create/Update `InventoryLevel` records.
    - [ ] Increment `Product` and `ProductVariant` global stock.
    - [ ] Log `StockLedger` entries (`transaction_type = Manual_Adjustment`, `section_name = Stock Adjustment`).

### Step 5: Finalization
- [ ] Run `./vendor/bin/pint --dirty`.
- [ ] Run `php artisan optimize`.
- [ ] Update `PROJECT_DOCUMENTATION.md`.

## 3. Verification Criteria
- [ ] Stock is correctly added to specified warehouse and batch.
- [ ] Serial numbers are tracked and searchable in batch reports.
- [ ] Global stock counts are accurately updated.
- [ ] Stock Ledger records the adjustment with correct financial values.
- [ ] Documentation updated.
