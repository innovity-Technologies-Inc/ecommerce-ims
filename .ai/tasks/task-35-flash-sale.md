# Task 35: Flash Sale Module

Implement a Flash Sale module with dynamic product selection, discount synchronization, and status management.

## Requirements
- REQ-52: Flash Sale Management (Single edit form for managing active/inactive status and global configuration).
- REQ-53: Optimized Product Selection (Paginated and searchable product list using FlexSearch for flash sale inclusion).
- REQ-54: Dynamic Discount Synchronization (Automatically apply/remove discounts to products and variants based on flash sale status or product removal).
- REQ-55: Flash Sale Tracking (Maintain a dedicated table for flash sale metadata and product associations).

## Steps

### 1. Database Setup
- [x] Create `FlashSale` model and migration (fields: `id`, `name`, `status`, `end_date`, timestamps).
- [x] Create `FlashSaleItem` model and migration (fields: `id`, `flash_sale_id`, `product_id`, `discount_amount`, `discount_type`, timestamps).
- [x] Add `is_flash_sale` boolean column to `products` table.

### 2. Backend Implementation (Service Layer & Controller)
- [x] Create `FlashSaleRequest` for validation.
- [x] Create `FlashSaleService` with the following methods:
    - `getFlashSale()`: Fetch the single flash sale record (create if none exists).
    - `updateFlashSale(array $data)`: Handle global status toggle and product updates.
    - `searchProducts(array $params)`: Return paginated/searchable products using FlexSearch (Updated to support filters/sorting).
    - `syncDiscounts(FlashSale $flashSale)`: 
        - If active: Apply discounts to `products` and `product_variants`.
        - If inactive: Reset all associated product/variant discounts to 0 and set `is_flash_sale` to false.
    - `resetProductDiscount(int $productId)`: Reset discount for a product and its variants.
- [x] Create `FlashSaleController` for Admin management (Updated to fetch categories/brands for filters).

### 3. Frontend Implementation (Admin Panel Refinements)
- [x] Add "Flash Sale" to the "Promotions" sidebar section.
- [x] Update Flash Sale edit form view:
    - [x] Add filters for Category, Subcategory, and Brand to the product selection panel.
    - [x] Add a Sort filter (Latest, Oldest, A-Z, Z-A).
    - [x] Implement reactive subcategory loading based on category selection.
    - [x] Ensure products load on initial page load with pagination.
    - [x] Fix image display issues in both the "Search Results" panel and "Selected Products" list.
- [x] Use jQuery for AJAX-based product search and dynamic list management within the form.

### 4. Verification
- [x] Verify that activating the sale applies discounts to base products.
- [x] Verify that activating the sale applies discounts to all variants of selected products.
- [x] Verify that deactivating the sale resets all discounts and the `is_flash_sale` flag.
- [x] Verify that removing a product from the list resets its specific discounts.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- Flash sale status correctly toggles logic across multiple tables.
- Search and pagination for product selection is performant.
- Discounts are accurately reflected in `products` and `product_variants` tables.
- Reset logic handles both global deactivation and individual product removal.
