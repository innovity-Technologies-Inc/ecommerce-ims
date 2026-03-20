# Task 51: Bulk Product Upload

Implement a bulk product and variant upload system using Excel/CSV.

## Requirements
- Use `maatwebsite/excel` package.
- Support importing products with their categories, brands, and base data.
- Support importing product variants associated with products.
- No image uploading in bulk.
- Handle mapping of category/brand names to their respective IDs.
- Follow Service Layer and Form Request patterns.

## Implementation Steps

### 1. Setup & Package Installation
- [x] Install `maatwebsite/excel` via composer.
- [x] Publish config if necessary.

### 2. Form Request & Routing
- [x] Create `BulkProductUploadRequest` for file validation (mimes: xlsx, xls, csv).
- [x] Add routes in `routes/web.php` (Admin group).
    - `GET /admin/products/import` - Show import form.
    - `POST /admin/products/import` - Process import.
    - `GET /admin/products/import-template` - Download Excel template.

### 3. Service & Import Logic
- [x] Create `ProductsImport` class implementing `ToCollection`, `WithHeadingRow`, `WithValidation`.
- [x] In `ProductService`, implement `importProducts(UploadedFile $file)` method.
- [x] Logic for finding/creating Category, Subcategory, and Brand by name.
- [x] Logic for creating/updating Products and their Variants.
- [x] Ensure slugs are generated correctly.

### 4. Controller Implementation
- [x] Add `importForm()`, `import()`, and `downloadTemplate()` to `ProductController`.
- [x] Ensure thin controller pattern: call `ProductService`.

### 5. UI Implementation
- [x] Create `resources/views/admin/product/import.blade.php`.
- [x] Add "Import Products" button to Product Index page.
- [x] Use Bootstrap 5 styling and standard project components.

### 6. Verification
- [x] Create a sample Excel file with products and variants.
- [x] Verify successful import of products.
- [x] Verify successful import of variants.
- [x] Verify error handling (invalid category, missing required fields).
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.

**Note:** Manual test files (CSV and XLSX) are located in the `test_data/` directory.

## Documentation Update
- [x] Update `PROJECT_DOCUMENTATION.md` with Bulk Upload module details.
