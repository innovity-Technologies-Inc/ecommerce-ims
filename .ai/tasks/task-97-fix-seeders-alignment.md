# Task 97: Fix Seeders and Align Product Seeder

## Requirement
Implement REQ-134: Fix all existing seeders to ensure they run without errors and align `ProductSeeder` logic with the current product creation service.

## Implementation Steps

### 1. Fix BrandSeeder
- Use `updateOrCreate` or `firstOrCreate` to prevent `Duplicate entry` errors during re-seeding.
- Ensure all required fields are present.

### 2. Fix CategorySeeder
- Ensure parent-child relationships are handled correctly.
- Use `updateOrCreate` to prevent unique constraint violations on slugs.

### 3. Align ProductSeeder
- Update `ProductSeeder` to match the `ProductService::storeProduct` requirements.
- Remove the `stock` field from variant data (stock should be 0 initially until a PO is received, or we can assume it's 0 by default).
- Ensure `min_stock_global` and `min_stock_type` are provided if necessary.
- Use `updateOrCreate` logic where possible or clean up before seeding.

### 4. General Seeder Cleanup
- Check `SliderSeeder`, `SectionSettingSeeder`, and `GeneralSettingSeeder` for missing fields or hardcoded values that might conflict.
- Ensure `DatabaseSeeder` order is optimal.

### 5. Verification
- Run `php artisan migrate:fresh --seed` to ensure a clean state.
- Run `php artisan db:seed` multiple times to ensure seeders are idempotent.

## Verification Criteria
- `php artisan db:seed` runs without any SQL errors.
- Products created via `ProductSeeder` appear correctly in the admin panel with their variants.
- No duplicate entries created when running seeders multiple times (idempotency).
