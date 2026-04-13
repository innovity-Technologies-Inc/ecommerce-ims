# Task 171: Idempotent Seeders (REQ-171)

Update all database seeders to use `updateOrCreate` or similar idempotent logic. This ensures that `php artisan db:seed` can be run multiple times without creating duplicate records or failing due to unique constraints.

## 1. Requirement Logging
- [x] Log REQ-171 in `.ai/requirements/requirements.md`.

## 2. Task Design
- [x] Analyze existing seeders.
- [ ] Refactor `ProductSeeder.php` to use idempotent logic instead of `delete()`.
- [ ] Verify all other seeders already use `updateOrCreate` or `findOrCreate`.

## 3. Surgical Implementation

### Step 1: Update ProductSeeder
- Modify `database/seeders/ProductSeeder.php`.
- Remove `Product::query()->delete();`.
- Use `updateOrCreate` based on product name or slug.
- Note: Since `storeProduct` service method handles variants and related data, we might need a more sophisticated check or update the service to handle idempotency.

## 4. Verification & Styling
- [ ] Run `php artisan db:seed` multiple times.
- [ ] Verify no duplicate products or variants are created.
- [ ] Run `./vendor/bin/pint --dirty`.
- [ ] Run `php artisan optimize`.

## 5. Documentation Update
- [ ] Update `PROJECT_DOCUMENTATION.md`.
