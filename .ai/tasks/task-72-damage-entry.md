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
- [ ] Create migration to update `wastages` table.
- [ ] Create migration to update `batch_serials` `stock_status` enum.
- [ ] Update `Wastage` model with new fields and relationships.
- [ ] Update `BatchSerial` model if needed.

### Step 2: Service Layer & Form Requests
- [ ] Create `DamageEntryService` (or extend `WastageService` if it exists).
- [ ] Create `DamageEntryRequest` for validation.

### Step 3: Admin Controller & UI
- [ ] Update `WastageController` (or create `DamageEntryController`).
- [ ] **Wastage Index Page:**
    - [ ] Add "Damage Entry" button.
- [ ] **Damage Entry Page (Create):**
    - [ ] Select Warehouse.
    - [ ] Select Batch (Filtered by warehouse).
    - [ ] Select Product/Variant (Filtered by batch).
    - [ ] Quantity input.
    - [ ] Serial selection:
        - [ ] Button to open modal with checkboxes for "Good" serials in selected batch/product.
        - [ ] Mandatory if serials exist for selection.
- [ ] **Wastage Show Page:**
    - [ ] Update to show Warehouse and Batch info.

### Step 4: Logic Integration (Inventory Synchronization)
- [ ] **On Creation:**
    - [ ] Create `Wastage` record.
    - [ ] If serials selected:
        - [ ] Update `batch_serials`: `product_status = 'damaged'`, `stock_status = 'wastage'`.
    - [ ] Update `InventoryLevel`: Decrement `current_quantity`, Increment `damaged_quantity`.
    - [ ] Update `Batch`: Decrement `total_saleable_qty`, Increment `total_damaged_qty`.
    - [ ] Update `BatchProduct`: Decrement `saleable_qty`, Increment `damaged_qty`.
    - [ ] Log `StockLedger`: `transaction_type = 'warehouse_damage'`, `reason_code = 'Warehouse Damage'`.

### Step 5: Finalization
- [ ] Run `./vendor/bin/pint --dirty`.
- [ ] Run `php artisan optimize`.
- [ ] Update `PROJECT_DOCUMENTATION.md`.

## 3. Verification Criteria
- [ ] Damage entry correctly reduces saleable stock and increases damaged stock.
- [ ] Serials are correctly marked as wastage.
- [ ] Stock Ledger records the warehouse damage transaction.
- [ ] UI follows existing patterns (PO Receive / RMA).
