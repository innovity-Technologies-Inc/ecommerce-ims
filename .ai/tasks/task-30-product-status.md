# Task: Product Status (Active/Discontinued)

Products should have an active/discontinued status that can be toggled in the admin panel. If a product is discontinued, it should display a red "Discontinued" badge on the client side.

## Implementation Steps

1.  **Migration**: Add `status` boolean column to the `products` table (default true/active).
2.  **Model**: Update `Product` model to include `status` in `$fillable` and add a cast for boolean.
3.  **Routes & Controller**: Add a route and controller method in `ProductController` to handle toggling the status via AJAX or form submission from the admin index.
4.  **Service**: Update `ProductService` to add a `toggleStatus` method. Also, ensure `store` and `update` methods optionally handle status.
5.  **Admin Views**: 
    - Update `resources/views/admin/products/index.blade.php` to include a status column with a toggle switch.
    - (Optional) Update `resources/views/admin/products/form.blade.php` to include a status dropdown/toggle.
6.  **Client Views**: Update `resources/views/client/partials/product_card.blade.php` and `resources/views/client/product_details.blade.php` to display a red badge "Discontinued" when `status` is false.
7.  **Client Logic**: Modify frontend queries to exclude discontinued products from showing up in default listings, or just show them with the badge (the requirement says "have a flag discontinued in the client side", so they probably still appear but with the badge, or maybe they just show the badge. We'll show the badge).

## Verification Criteria

- [x] Migration executed successfully.
- [x] Admin can toggle a product's status on the index page.
- [x] Toggled status reflects in the database.
- [x] Client product card shows "Discontinued" badge if status is false.
- [x] Client product details page shows "Discontinued" badge if status is false.
