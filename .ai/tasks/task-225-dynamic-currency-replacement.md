# Task: Dynamic Currency Replacement (REQ-225)

## Requirement
Replace all remaining hardcoded `$` symbols with dynamic currency in the following list of files.

## Instructions
1. Define `$gs = \App\HelperClass::generalSettings();` if not available.
2. Replace `${{` with `{{ $gs->currency ?? '$' }}{{`.
3. Replace `-$` with `-{{ $gs->currency ?? '$' }}`.

## Files to Update
- `resources/views/emails/orders/status_update.blade.php`
- `resources/views/emails/returns/status_update.blade.php`
- `resources/views/emails/returns/confirmation.blade.php`
- `resources/views/emails/orders/confirmation.blade.php`
- `resources/views/client/cart.blade.php`
- `resources/views/client/track-order.blade.php`
- `resources/views/client/structure/partials/header.blade.php`
- `resources/views/client/structure/mini-cart.blade.php`
- `resources/views/client/returns/partials/order_items.blade.php`
- `resources/views/client/checkout/index.blade.php`
- `resources/views/client/account/orders.blade.php`
- `resources/views/admin/reports/warehouse-performance/index.blade.php`
- `resources/views/admin/returns/show_request.blade.php`
- `resources/views/admin/reports/customers/list.blade.php`
- `resources/views/admin/reports/customers/index.blade.php`

## Implementation Steps
1. [ ] Iterate through each file and apply the replacements.
2. [ ] Ensure `$gs` is properly initialized at the top of the file or section.
3. [ ] Run `vendor/bin/pint --dirty` to maintain styling.
4. [ ] Run `php artisan optimize` to refresh caches.

## Verification Criteria
- [ ] Verify that all specified files use `{{ $gs->currency ?? '$' }}` instead of hardcoded `$`.
- [ ] Check if `$gs` is defined in all modified files.
- [ ] Verify there are no syntax errors in the Blade templates.
