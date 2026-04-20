# Task 219: Stock-Aware Add to Cart Button

Disable the "Add to Cart" button when a product or specific variant is out of stock.

## Requirement Reference
- **REQ-219:** Stock-Aware Add to Cart Button.

## Implementation Steps

### 1. Helper Update
- **File:** `app/HelperClass.php`
- **Action:** Add `getProductTotalStock($product)` method.
- **Logic:** 
    - If `$product->variants->count() > 0`, return `$product->variants->sum('stock')`.
    - Else, return `$product->stock`.

### 2. UI Update: Product Card (Grid View)
- **File:** `resources/views/client/partials/product_card.blade.php`
- **Action:** 
    - Check total stock using the new helper.
    - If total stock <= 0, display a disabled button with "OUT OF STOCK" text.

### 3. UI Update: Product Listing (List View)
- **File:** `resources/views/client/products.blade.php`
- **Action:** 
    - Apply same stock-checking logic to the list view's "Add to Cart" section.

### 4. UI Update: Product Details Page
- **File:** `resources/views/client/product_details.blade.php`
- **Action:** 
    - Update the "Add to Cart" button to support a disabled state.
    - **JavaScript:** Update the variant selector change handler to:
        - Toggle the "Add to Cart" button's disabled state.
        - Update button text to "Out of Stock" if stock is 0, otherwise "+ Add To Cart".
    - Handle the initial state on page load for the first selected variant.

### 5. Verification
- Verify that a product with 0 stock shows "OUT OF STOCK" and cannot be clicked in grid/list views.
- Verify that on the details page, selecting an out-of-stock variant disables the button.
- Verify that selecting an in-stock variant enables the button.

## Documentation Update
- Update `PROJECT_DOCUMENTATION.md` to reflect the stock-aware cart button functionality.
