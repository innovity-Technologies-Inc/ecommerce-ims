# Task-150: Remove Batch Filter from Warehouse Report

## Objective
Remove the "Batch #" search filter from the "Warehouse-wise Valuation" inventory report to simplify the UI and focus on relevant aggregate data.

## Implementation Steps
- [x] Update `resources/views/admin/reports/inventory.blade.php`.
- [x] Wrap "Batch #" filter in a conditional `@if(($view ?? '') !== 'warehouse')`.
- [x] Adjust the "Generate" button's column width to `@if(($view ?? '') === 'warehouse' ? '4' : '2')` to fill the resulting empty space.
- [x] Run `php artisan optimize`.

## Verification
- [x] Open the Inventory Valuation report dashboard. Verify "Batch #" is visible.
- [x] Open the Warehouse-wise Valuation detailed report. Verify "Batch #" is hidden.
- [x] Verify the layout remains balanced in both view modes.
