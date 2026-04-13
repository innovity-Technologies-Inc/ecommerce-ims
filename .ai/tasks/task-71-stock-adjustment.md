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
- [x] Create migration for `stock_adjustments` and `stock_adjustment_items`.
- [x] Create models: `StockAdjustment`, `StockAdjustmentItem`.
- [x] Define relationships in `Warehouse`, `Batch`, `Product`, `ProductVariant`, `Admin`.

### Step 2: Service Layer & Form Requests
- [x] Create `StockAdjustmentService` to handle complex multi-table logic.
- [x] Create `StockAdjustmentRequest` for validation.

### Step 3: Admin Controller & UI
- [x] Create `StockAdjustmentController`.
- [x] **Index Page:**
    - [x] List all adjustments with FlexSearch (filters: warehouse, date).
- [x] **Create Page:**
    - [x] Select Warehouse.
    - [x] Enter/Select Batch Number.
    - [x] Dynamic Product/Variant selection.
    - [x] Quantity, Unit Cost inputs.
    - [x] Serial Number tagging (if applicable).
- [x] **Show Page:**
    - [x] Detailed view of adjustment and its impact.

### Step 4: Logic Integration (Inventory Synchronization)
- [x] **On Creation:**
    - [x] Create or Update `Batch` (supplier_id will be null).
    - [x] Create `BatchProduct` records.
    - [x] Create `BatchSerial` records (stock_status = 'in_stock', product_status = 'good').
    - [x] Create/Update `InventoryLevel` records.
    - [x] Increment `Product` and `ProductVariant` global stock.
    - [x] Log `StockLedger` entries (`transaction_type = Manual_Adjustment`, `section_name = Stock Adjustment`).

### Step 5: Finalization
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## 3. Verification Criteria
- [x] Stock is correctly added to specified warehouse and batch.
- [x] Serial numbers are tracked and searchable in batch reports.
- [x] Global stock counts are accurately updated.
- [x] Stock Ledger records the adjustment with correct financial values.
- [x] Documentation updated.
