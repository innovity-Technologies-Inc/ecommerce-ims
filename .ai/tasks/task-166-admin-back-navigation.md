# Task-166: Admin Back Navigation

## Objective
Add "Back" buttons to all detailed (show) pages and specific index pages linked from the dashboard (Orders, Products, Customers, Best Selling, Low Stock) to improve user navigation and experience.

## Implementation Steps

### 1. Show Pages
- [x] Add "Back to List" button to `resources/views/admin/contact_messages/show.blade.php`.
- [x] Add "Back to List" button to `resources/views/admin/customers/show.blade.php`.
- [x] Add "Back to List" button to `resources/views/admin/inventory/adjustment/show.blade.php`.
- [x] Add "Back to List" button to `resources/views/admin/inventory/batches/show.blade.php`.
- [x] Add "Back to List" button to `resources/views/admin/inventory/damaged/show.blade.php`.
- [x] Add "Back to List" button to `resources/views/admin/inventory/rma/show.blade.php`.
- [x] Add "Back to List" button to `resources/views/admin/inventory/stock/show.blade.php`.
- [x] Add "Back to List" button to `resources/views/admin/inventory/suppliers/show.blade.php`.
- [x] Add "Back to List" button to `resources/views/admin/orders/show.blade.php`.
- [x] Add "Back to List" button to `resources/views/admin/reports/warehouse-performance/show.blade.php`.

### 2. Index Pages
- [x] Add "Back to Dashboard" button to `resources/views/admin/orders/index.blade.php`.
- [x] Add "Back to Dashboard" button to `resources/views/admin/products/index.blade.php`.
- [x] Add "Back to Dashboard" button to `resources/views/admin/customers/index.blade.php`.
- [x] Add "Back to Dashboard" button to `resources/views/admin/products/best-selling.blade.php`.
- [x] Add "Back to Dashboard" button to `resources/views/admin/products/low-stock.blade.php`.

### 3. Finalization
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- All "show" pages have a "Back" button linking to the corresponding list.
- Primary index pages linked from the dashboard have a "Back to Dashboard" button.
- Buttons use consistent Bootstrap 5 styling and icons.
