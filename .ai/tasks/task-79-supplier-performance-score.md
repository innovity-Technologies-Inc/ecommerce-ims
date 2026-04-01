# Task 79: Supplier Performance Score Implementation

Develop a performance scoring system for suppliers based on Purchase Order (PO) fulfillment. Each PO will receive a score upon receipt, and the average score for each supplier will be displayed in the Supplier index.

## 1. Requirement Details
- **REQ-116:** Supplier Performance Score calculation and display.
- **Score Components:**
    - **Delivery Score (40%):** Awarded if the PO is received on or before the `expected_delivery_date`.
    - **Quality Score (60%):** Based on the ratio of good products vs. total products received in the PO batches.
- **Trigger:** Score calculation occurs during the PO "Receive" process when the status changes to `Delivered`.

## 2. Implementation Plan

### Phase 1: Database Updates
- [ ] Create a migration to add `performance_score` (decimal 5,2) to the `purchase_orders` table.

### Phase 2: Service Layer Logic
- [ ] Update `PurchaseOrderService::receivePurchaseOrder` to:
    - [ ] Calculate the Delivery Score (40 points if `received_date` <= `expected_delivery_date`).
    - [ ] Calculate the Quality Score (60 * `total_received` / (`total_received` + `total_damaged`)).
    - [ ] Store the sum as `performance_score` in the `purchase_orders` table.

### Phase 3: Supplier Model & Service
- [ ] Add an accessor or method to the `Supplier` model to calculate the average performance score of all `Delivered` POs.
- [ ] Alternatively, handle the average calculation in the `InventoryService` (or whichever service handles Suppliers).

### Phase 4: UI Updates (Admin Panel)
- [ ] **Purchase Order View:** Display the performance score in the PO details page.
- [ ] **Supplier Index:** Add a "Performance Score" column to the suppliers table showing the average score with a star icon or color-coded badge.

## 3. Verification Criteria
- [ ] Create a PO with an expected delivery date.
- [ ] Receive the PO on/before the date with some damaged items.
- [ ] Verify the `performance_score` is correctly calculated and stored.
- [ ] Verify the Supplier index shows the correct average score across multiple POs.
- [ ] Run `./vendor/bin/pint --dirty`.
- [ ] Run `php artisan optimize`.

## 4. Documentation
- [ ] Update `PROJECT_DOCUMENTATION.md` with the new Supplier Performance module details.
