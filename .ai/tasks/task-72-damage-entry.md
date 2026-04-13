# Task 72: Damage Entry (Warehouse Wastage) Module

Implement a module to record products damaged within the warehouse, integrating with Wastage, Batch Serials, and Inventory Levels.

## 1. Database Schema

### `wastages` Table Updates
- Add `warehouse_id` (Foreign Key to `warehouses`).
- Add `batch_id` (Foreign Key to `batches`).
- Add `created_by` (Foreign Key to `admins`).

### `batch_serials` Table Updates
- Update `stock_status` enum to include `wastage`.

## 2. Implementation Steps

### Step 1: Backend Setup (Migrations & Models)
- [x] Create migration to update `wastages` table.
- [x] Create migration to update `batch_serials` `stock_status` enum.
- [x] Update `Wastage` model with new fields and relationships.
- [x] Update `BatchSerial` model if needed.

### Step 2: Service Layer & Form Requests
- [x] Create `DamageEntryService` (or extend `WastageService` if it exists).
- [x] Create `DamageEntryRequest` for validation.

### Step 3: Admin Controller & UI
- [x] Update `WastageController` (or create `DamageEntryController`).
- [x] **Wastage Index Page:**
    - [x] Add "Damage Entry" button.
- [x] **Damage Entry Page (Create):**
    - [x] Select Warehouse.
    - [x] Select Batch (Filtered by warehouse).
    - [x] Select Product/Variant (Filtered by batch).
    - [x] Quantity input.
    - [x] Serial selection:
        - [x] Button to open modal with checkboxes for "Good" serials in selected batch/product.
        - [x] Mandatory if serials exist for selection.
- [x] **Wastage Show Page:**
    - [x] Update to show Warehouse and Batch info.

### Step 4: Logic Integration (Inventory Synchronization)
- [x] **On Creation:**
    - [x] Create `Wastage` record.
    - [x] If serials selected:
        - [x] Update `batch_serials`: `product_status = 'damaged'`, `stock_status = 'wastage'`.
    - [x] Update `InventoryLevel`: Decrement `current_quantity`, Increment `damaged_quantity`.
    - [x] Update `Batch`: Decrement `total_saleable_qty`, Increment `total_damaged_qty`.
    - [x] Update `BatchProduct`: Decrement `saleable_qty`, Increment `damaged_qty`.
    - [x] Log `StockLedger`: `transaction_type = 'warehouse_damage'`, `reason_code = 'Warehouse Damage'`.

### Step 5: Finalization
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## 3. Verification Criteria
- [x] Damage entry correctly reduces saleable stock and increases damaged stock.
- [x] Serials are correctly marked as wastage.
- [x] Stock Ledger records the warehouse damage transaction.
- [x] UI follows existing patterns (PO Receive / RMA).
