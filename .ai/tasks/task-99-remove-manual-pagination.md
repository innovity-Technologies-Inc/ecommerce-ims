# Task: Remove Manual Pagination Info Blocks

## Description
Remove the manual "Showing X to Y of Z Results" text blocks from all admin table partials. Laravel's `links()` method already provides this information and the layout, leading to duplication.

## Files to Update
- resources/views/admin/users/partials/table.blade.php
- resources/views/admin/brands/partials/table.blade.php
- resources/views/admin/categories/partials/table.blade.php
- resources/views/admin/products/partials/table.blade.php
- resources/views/admin/sliders/partials/table.blade.php
- resources/views/admin/shipping_methods/partials/table.blade.php
- resources/views/admin/orders/partials/table.blade.php
- resources/views/admin/inventory/warehouses/partials/table.blade.php
- resources/views/admin/inventory/stock/partials/table.blade.php
- resources/views/admin/inventory/suppliers/partials/table.blade.php
- resources/views/admin/inventory/po/partials/table.blade.php
- resources/views/admin/contact_messages/partials/table.blade.php
- resources/views/admin/customers/partials/table.blade.php
- resources/views/admin/inventory/batches/partials/table.blade.php
- resources/views/admin/inventory/damaged/partials/table.blade.php

## Implementation Steps
1. Search each file for the `card-footer` section containing the manual pagination info.
2. Replace the nested `d-flex` block with a direct call to `{{ $data->links() }}` (or the corresponding variable).
3. Ensure the variable name matches the one used in each file.

## Verification
1. Manually check the layout of the admin tables after modification.
2. Ensure no duplication of "Showing..." text exists.
3. Verify that `links()` still displays correctly in the card footer.
4. Run `php artisan optimize`.
