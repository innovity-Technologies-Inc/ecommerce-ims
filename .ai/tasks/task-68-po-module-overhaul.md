# Task 68: PO Module & Inventory Overhaul

Refactor the Purchase Order (PO) receiving process and inventory management to remove the quarantine warehouse dependency and restructure batch tracking for granular product status management.

## Requirements
- **REQ-103:** PO Module & Inventory Overhaul: Remove quarantine warehouse dependency. Restructure DB to use `batches`, `batch_products`, and `batch_serials`. Implement `product_status` (good, damaged, damaged_return) in serials. Update all inventory reports to focus on product-wise stock and batch details.

## Implementation Steps

### 1. Database Restructuring (Migration)
- **File:** `database/migrations/xxxx_restructure_inventory_tables.php`
- **Action:**
    - Create `batch_products` table (`id`, `batch_id`, `product_id`, `product_variant_id`, `received_qty`, `saleable_qty`, `damaged_qty`).
    - Update `batches` table:
        - Remove `product_id`, `product_variant_id`, `quantity`.
        - Add `total_received_qty`, `total_saleable_qty`, `total_damaged_qty`.
    - Update `batch_serials` table:
        - Rename `status` to `product_status`.
        - Add `product_status` enum/string (good, damaged, damaged_return).
    - Update `inventory_levels` table:
        - Add `damaged_quantity` (so a single warehouse record can track both saleable and damaged stock).

### 2. Model Updates
- **Models:** `Batch`, `BatchProduct`, `BatchSerial`, `InventoryLevel`.
- **Action:** Define new relationships and fillable fields.

### 3. Service Layer Refactoring
- **`PurchaseOrderService::receivePurchaseOrder`:** 
    - Create a single `Batch` header per receiving event.
    - Create `BatchProduct` records for each item in the PO.
    - Store serials in `batch_serials` with the appropriate `product_status`.
    - Update `InventoryLevel` within the target warehouse (no more quarantine warehouse movement).
- **`InventoryService`:** Update report fetching logic to support the new schema.

### 4. UI/UX Refinement
- **PO Receive Form:** Maintain the auto-calculation but ensure data is submitted to the new structure.
- **Stock Report:** Show `saleable_qty` and `damaged_qty` separately in the table. "Details" view should show warehouse-wise product breakdown.
- **Batch Tracking:** List unique batches. "Details" view shows all products (`BatchProduct`) and their serials grouped by status.
- **Damaged Products Report:** Focus on items with `damaged_qty > 0` or serials with `damaged` status.

### 5. Cleanup
- Remove the "Quarantine" warehouse from the database (via seeder/migration cleanup or manual check).
- Remove logic that previously moved items to the quarantine warehouse.

## Verification Criteria
- A single batch is created for each PO receipt.
- Serial numbers correctly store status (good/damaged).
- Damaged items stay in the target warehouse but are tracked in a separate `damaged_quantity` field.
- Reports correctly display Total, Saleable, and Damaged counts.
- Navigation between Stock, Batches, and Damaged products is consistent.
