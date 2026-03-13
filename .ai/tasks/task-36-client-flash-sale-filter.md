# Task 36: Client-side Flash Sale Filter

Add a filter to the Client Shop page to allow customers to filter products by Flash Sale association using the Sale Title.

## Requirements
- REQ-58: Client-side Flash Sale Filter (Add a filter to the shop page to filter products by Flash Sale title using FlexSearch).

## Steps

### 1. Backend Implementation (Controller)
- [x] Update `FrontendController@products` to handle `flash_sale_id` filter.
- [x] Ensure `FlexSearch` is used for the filtering logic.
- [x] Fetch active Flash Sales for the sidebar filter dropdown.

### 2. Frontend Implementation (Client Panel)
- [x] Update `resources/views/client/products.blade.php` to add a "Flash Sale" filter widget in the sidebar.
- [x] Update AJAX filtering logic to include the new Flash Sale filter.

### 3. Verification
- [x] Verify that selecting a Flash Sale from the sidebar filters products correctly on the shop page.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- Customers can filter shop products by Flash Sale.
- The filter works with AJAX without page reload.
- FlexSearch is used for the backend logic.
