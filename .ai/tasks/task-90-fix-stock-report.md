# Task 90: Fix Stock Report Calculation (COMPLETED)

## Requirement
Fix the stock calculation discrepancy in the Stock Report (REQ-128). The report is currently showing incorrect stock levels due to row duplication in the JOIN between `inventory_levels` and `batch_products`.

## Implementation Steps
1. **Research & Verify:** Confirm the row duplication issue in `ReportService::getStockReport` by joining on `product_variant_id` and handling NULL values.
2. **Surgical Fix:** Update `ReportService::getStockReport` to correctly join `inventory_levels` and `batch_products` on `batch_id`, `product_id`, AND `product_variant_id`.
3. **Alignment:** Ensure valuation logic in `getStockReport` aligns with `getCurrentInventory` (Inventory Valuation).
4. **Verification:**
    - Run the query manually to ensure no duplication.
    - Verify the Stock Report in the Admin Panel.
    - Run existing Seeders to ensure data integrity.
    - Run `php artisan optimize`.

## Verification Criteria
- Stock Report totals and row-level data must match Inventory Valuation for the same filters.
- Low stock alerts must be accurate.
- No duplicate rows for products with multiple variants in the same batch.
- `php artisan test` (if applicable) and manual verification with seeded data.
