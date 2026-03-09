# Task 11: Fix Database Seeders

Fix all database seeders that do not match the current model schemas and fields.

## 1. Requirement
- **REQ-26:** Database Seeder Alignment (Fix seeders to match current model schemas and fields).

## 2. Implementation Steps
- [x] **AdminSeeder**: Verify fields (name, email, password).
- [x] **UserSeeder**: Verify fields (name, email, password).
- [x] **BrandSeeder**: Remove `status`, add `icon` placeholder if needed.
- [x] **CategorySeeder**: Ensure sub-category hierarchy is correct, add `icon` placeholder if needed.
- [x] **GeneralSettingSeeder**: Completely rewrite to match `business_name`, `meta_title`, `currency`, etc.
- [x] **MailSettingSeeder**: Verify fields match schema.
- [x] **ProductSeeder**: Verify it uses `ProductService` correctly and all fields are passed.
- [x] **SectionSettingSeeder**: Add `background_image` placeholder if needed, verify `mode`.
- [x] **SliderSeeder**: Verify fields (image, title, subtitle, subtext, button_name, button_url, is_active, position).

## 3. Verification Criteria
- [x] Run `php artisan migrate:fresh --seed` to ensure all seeders run without errors.
- [x] Verify database data using `database-query` or `tinker`.
