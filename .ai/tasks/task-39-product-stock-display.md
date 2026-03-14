# Task 39: Product Stock Display

Implement real-time stock level visibility on Product Details pages for both Admins and Customers, supporting both base and variant-specific inventory.

## Requirements
- REQ-63: Product Stock Display (Admin and Client interfaces).

## Steps

### 1. Database & Model Preparation
- [x] Add `stock` column to `products` table (Base Stock).
- [x] Update `Product` model `$fillable` and `$casts`.
- [x] Update `ProductRequest` validation rules.
- [x] Update `ProductService` (`storeProduct` and `updateProduct`) to handle base stock.

### 2. Admin Panel Updates
- [x] Restore "Base Stock Quantity" field in `admin/products/form.blade.php`.
- [x] Add "Base Stock" row to the product details view in `admin/products/show.blade.php`.

### 3. Customer Frontend Updates
- [x] Add "Availability" status badge to `client/product_details.blade.php`.
- [x] Implement dynamic stock updates via JavaScript when a variant is selected.
- [x] Automatically hide "Add to Cart" if the selected item is out of stock.

### 4. Documentation
- [x] Update `PROJECT_DOCUMENTATION.md` (Section 3.20).
- [x] Update `requirements.md` (REQ-63).

## Verification Criteria
- Admin can set and view base stock for simple products.
- Customers see accurate stock levels for each variant.
- Selecting an "Out of Stock" variant disables the purchase action.
- Layout remains clean and professionally aligned.
