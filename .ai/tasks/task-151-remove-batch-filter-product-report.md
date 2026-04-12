# Task-151: Remove Batch Filter from Product Report

## Objective
Remove the "Batch #" search filter from the "Product-wise Valuation" inventory report to simplify the UI and focus on relevant aggregate data.

## Implementation Steps
- [x] Update `resources/views/admin/reports/inventory.blade.php`.
- [x] Update the conditional check to `@if(!in_array($view ?? '', ['warehouse', 'product']))`.
- [x] Update the "Generate" button's column width to `@if(in_array($view ?? '', ['warehouse', 'product']) ? '4' : '2')`.
- [x] Run `php artisan optimize`.

## Verification
- [x] Open the Inventory Valuation report dashboard. Verify "Batch #" is visible.
- [x] Open the Warehouse-wise Valuation detailed report. Verify "Batch #" is hidden.
- [x] Open the Product-wise Valuation detailed report. Verify "Batch #" is hidden.
- [x] Verify the layout remains balanced in all view modes.
