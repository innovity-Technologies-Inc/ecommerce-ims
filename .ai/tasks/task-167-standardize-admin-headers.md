# Task: Standardize Admin Create/Edit Headers

Standardize the 'create' and 'edit' pages in the admin panel by moving page titles and adding 'Back' buttons.

## Requirements
- **REQ-167:** Move page title from `card-header` to a new `d-flex` header section above the `card`.
- Add a 'Back' button in that header linking to the corresponding list page.
- Use `btn btn-secondary btn-sm` and `<i class="bx bx-arrow-back me-1"></i>`.
- Remove redundant `card-header`.

- [x] `resources/views/admin/brands/form.blade.php` (Back to `admin.brands.index`)
- [x] `resources/views/admin/categories/form.blade.php` (Back to `admin.categories.index`)
- [x] `resources/views/admin/coupons/form.blade.php` (Back to `admin.coupons.index`)
- [x] `resources/views/admin/flash_sale/edit.blade.php` (Add Back to `admin.dashboard` to the existing header)
- [x] `resources/views/admin/inventory/adjustment/create.blade.php` (Back to `admin.inventory.adjustment.index`)
- [x] `resources/views/admin/inventory/po/create.blade.php` (Back to `admin.inventory.po.index`)
- [x] `resources/views/admin/inventory/po/edit.blade.php` (Back to `admin.inventory.po.index`)
- [x] `resources/views/admin/inventory/rma/create.blade.php` (Back to `admin.inventory.rma.index`)
- [x] `resources/views/admin/inventory/suppliers/form.blade.php` (Back to `admin.suppliers.index`)
- [x] `resources/views/admin/inventory/warehouses/form.blade.php` (Back to `admin.warehouses.index`)
- [x] `resources/views/admin/products/form.blade.php` (Back to `admin.products.index`)
- [x] `resources/views/admin/products/import.blade.php` (Back to `admin.products.index`)
- [x] `resources/views/admin/roles/form.blade.php` (Back to `admin.roles.index`)
- [x] `resources/views/admin/sections/form.blade.php` (Add Back to `admin.dashboard` to the existing header)
- [x] `resources/views/admin/shipping_methods/create.blade.php` (Back to `admin.shipping_methods.index`)
- [x] `resources/views/admin/shipping_methods/edit.blade.php` (Back to `admin.shipping_methods.index`)
- [x] `resources/views/admin/sliders/create.blade.php` (Back to `admin.sliders.index`)
- [x] `resources/views/admin/sliders/edit.blade.php` (Back to `admin.sliders.index`)
- [x] `resources/views/admin/sliders/form.blade.php` (Back to `admin.sliders.index`)
- [x] `resources/views/admin/users/forms.blade.php` (Back to `admin.index`)

### 3. Finalization
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.
- [x] Update `requirements.md`.
