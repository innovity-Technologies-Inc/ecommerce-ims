# Task-148: Stock Report Export Buttons

## Objective
Add "Export" buttons next to the "Print" buttons in all dashboard cards of the Stock Reports for better accessibility to granular data.

## Implementation Steps
- [x] Update `resources/views/admin/reports/stock.blade.php`.
- [x] Add `btn-group` with Export (Excel) and Print buttons for all dashboard cards.
- [x] Use correct `view` parameter for each export route.
- [x] Run `php artisan optimize`.

## Verification
- [x] Export button is visible in all cards.
- [x] Clicking Export downloads the correct Excel file.
- [x] Print button still functions correctly.
