# Task 95: Update Gross Fill Rate Logic to Use Stock Ledger

## Requirement
Update the calculation logic for "Gross Fill Rate" in the Warehouse Performance Report (REQ-132) to use the `stock_ledger` table as the primary source of truth.

## Implementation Steps

### 1. Update WarehousePerformanceService
- Modify `App\Services\WarehousePerformanceService::calculateWarehouseMetrics`.
- Replace the complex query for `totalOrderedUnits` with a simpler query using the `stock_ledgers` table.
- Logic: `totalOrderedUnits` for a warehouse during a period is the absolute sum of `change_qty` for all `transaction_type = 'SALE'` entries in that period.
- This represents the "Gross Demand" or "Initial Shipment" assigned to that warehouse.
- Ensure `unitsShipped` (numerator) also uses the same `SALE` transactions (it already does).

### 2. Verification
- **Seeder-Driven Verification:** Verify the report dashboard and detail views.
- **Gross Fill Rate:** Should be 100% for warehouses where everything assigned was shipped (since the system only records shipped sales in the ledger).
- **Net Fill Rate:** Should correctly reflect `(Shipped - Returned) / Shipped`.

### 3. Documentation & Finalization
- Update `PROJECT_DOCUMENTATION.md` if necessary.
- Run `php artisan optimize`.
- Run `./vendor/bin/pint --dirty`.

## Verification Criteria
- Gross Fill Rate calculation matches the new ledger-based logic.
- Net Fill Rate calculation remains accurate relative to initial shipment.
- No regressions in other warehouse performance metrics.
