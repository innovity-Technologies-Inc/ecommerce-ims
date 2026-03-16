# Task 45: Accurate Price Sorting Fix

Ensure the Shop page's "Price: Low to High" and "Price: High to Low" sorting correctly handles hybrid products (those with base prices vs. variant-only prices).

## Requirements
- Refactor `FrontendService::getFilteredProducts()` to calculate a reliable "effective price" for sorting.
- Prioritize product base prices (discounted first).
- Fall back to the lowest variant price if no base price exists.
- Ensure consistent sorting for both ascending and descending directions.

## Implementation Steps
1. **Service Refactor:** Updated `FrontendService.php` to use a `DB::raw` `CASE` statement within `addSelect`.
2. **Logic Integration:** Added logic to check `products.regular_price > 0` before checking variant prices.
3. **Sorting Update:** Replaced `orderByRaw` with standard `orderBy` on the calculated `sort_price` alias.
4. **Formatting:** Ran Pint to ensure code quality.

## Verification Criteria
- [x] Products with variants and no base price sort correctly among products with base prices.
- [x] "Price: Low to High" shows the cheapest effective prices first.
- [x] "Price: High to Low" shows the most expensive effective prices first.
- [x] No SQL ambiguity errors on the shop page.
- [x] `./vendor/bin/pint --dirty` is run.
- [x] `php artisan optimize` is run.
