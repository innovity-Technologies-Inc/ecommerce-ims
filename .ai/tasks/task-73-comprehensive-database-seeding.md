# Task: 73 - Comprehensive Fashion Database Seeding

## Requirement
Seed the database with a robust set of initial fashion-related data including 20 USA state-based warehouses, 15 suppliers, and 100 fashion products (with variants). For each product, download and upload at least 3 relevant fashion images. Populate stock using Purchase Orders and Stock Adjustments.

## Objectives
1.  **Warehouses**: Add at least 20 warehouses named after USA states.
2.  **Suppliers**: Add at least 15 fashion-related suppliers (e.g., Textile Mills, Shoe Factories, Apparel Brands).
3.  **Products**: Add 100 fashion products across categories like Men's Fashion, Women's Fashion, Shoes, and Accessories.
4.  **Variants**: Include Size (S, M, L, XL) and Color (Black, White, Blue, Red) for most products.
5.  **Images**: Add at least 3 relevant fashion images per product (downloaded from `https://loremflickr.com/800/600/fashion` and uploaded).
6.  **Stock Operations**: 
    *   Create Purchase Orders for all products.
    *   Receive these Purchase Orders to populate stock and batches.
    *   Perform Stock Adjustments for some products.

## Implementation Steps

### 1. Warehouses (Step 1)
- Update `database/seeders/WarehouseSeeder.php` to include 20 USA state-based warehouses (e.g., "California Hub", "Texas Logistics Center").

### 2. Suppliers (Step 2)
- Update `database/seeders/SupplierSeeder.php` to include 15 fashion suppliers (e.g., "Global Textiles", "Urban Fabrics", "Sole Manufacturers").

### 3. Products & Stock (Step 3)
- Update `database/seeders/ProductSeeder.php` to:
    - Clear existing products and related data (images, variants, stocks, batches).
    - Generate 100 products using fashion categories and brands.
    - For each product:
        - Generate 3 fashion images (download from `https://loremflickr.com/800/600/fashion`).
        - Create 0-3 variants per product (Size/Color combinations).
        - Create a `PurchaseOrder` for the product/variants (status: `Sent`).
        - Call `PurchaseOrderService::receivePurchaseOrder` to stock the items in a random warehouse.
        - Create a `StockAdjustment` for a subset of products to test adjustment history.

### 4. Image Downloading Strategy
- Use `Http::get` to fetch image content.
- Store temporarily in `storage/app/temp_seed_images`.
- Create a `UploadedFile` wrapper to compatible with `HelperClass::file_upload`.

## Verification Criteria
1.  Run `php artisan db:seed --class=WarehouseSeeder`.
2.  Run `php artisan db:seed --class=SupplierSeeder`.
3.  Run `php artisan db:seed --class=ProductSeeder`.
4.  Verify 20 warehouses in the Admin Panel.
5.  Verify 15 suppliers in the Admin Panel.
6.  Verify 100 fashion products with images and stock in the Shop and Admin Panel.
7.  Verify Purchase Order history and Stock Adjustment history in the Admin Panel.
