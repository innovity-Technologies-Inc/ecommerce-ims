# Task: Global Currency Standardization (REQ-223)

Replace all hardcoded currency symbols (e.g., "$") with the dynamic setting from `HelperClass::generalSettings()->currency`.

## 1. Requirement Detail
- **What:** Ensure the application respects the user-defined currency symbol globally.
- **How:** 
    - Identify all Blade files with hardcoded "$".
    - Replace them with `{{ \App\HelperClass::generalSettings()->currency ?? '$' }}` or `$gs->currency` if `$gs` is already defined.
- **Scope:** 
    - Admin Reports (Sales, Inventory, Stock, Customers)
    - Admin Orders (Show, Invoice)
    - Client Frontend (Cart, Checkout, Account, Track Order)
    - Emails (Order Confirmation, Status Updates, Returns)

## 2. Implementation Steps

### Step 1: Replace in Reports
- `resources/views/admin/reports/sales.blade.php`
- `resources/views/admin/reports/inventory.blade.php`
- `resources/views/admin/reports/stock.blade.php`
- `resources/views/admin/reports/customers/*.blade.php`
- `resources/views/admin/reports/warehouse-performance/*.blade.php`

### Step 2: Replace in Orders & Returns
- `resources/views/admin/orders/show.blade.php`
- `resources/views/admin/orders/invoice.blade.php`
- `resources/views/admin/returns/show_request.blade.php`

### Step 3: Replace in Client Views
- `resources/views/client/cart.blade.php`
- `resources/views/client/checkout/index.blade.php`
- `resources/views/client/account/*.blade.php`
- `resources/views/client/track-order.blade.php`
- `resources/views/client/structure/mini-cart.blade.php`
- `resources/views/client/structure/partials/header.blade.php`

### Step 4: Replace in Emails
- `resources/views/emails/orders/*.blade.php`
- `resources/views/emails/returns/*.blade.php`
- `resources/views/mail/purchase-order.blade.php`

### Step 5: Verification
- Change currency in General Settings.
- Verify the change reflects across all identified pages and emails.
- Run `php artisan optimize`.

## 3. Verification Criteria
- [ ] No hardcoded "$" remains in the identified files.
- [ ] Dynamic currency is correctly displayed based on settings.
- [ ] Documentation updated.
