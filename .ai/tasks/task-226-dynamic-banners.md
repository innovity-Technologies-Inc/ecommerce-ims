# Task: Dynamic Banners (REQ-226)

Make 4 homepage banners and 1 cart page banner dynamic via the admin panel.

## 1. Requirement Detail
- **What:** Admin can upload 5 specific banners to replace the current static images.
- **How:** 
    - Create a `banners` table with `slug` (unique position identifier) and `image`.
    - Create an admin interface to manage these 5 spots.
    - Update frontend views (`banner_1.blade.php`, `banner_2.blade.php`, `cart.blade.php`) to fetch these images.
- **Positions & Sizes:**
    - `home_1_left`: 330x315
    - `home_1_middle`: 690x315
    - `home_1_right`: 330x315
    - `home_2_full`: 1410x230
    - `cart_sidebar`: 690x550
    - `menu_banner`: 1350x170

## 2. Implementation Steps

### Step 1: Database & Model
- Create migration for `banners` table: `id`, `slug` (unique), `image`, `link`, `created_by`, `updated_by`, `timestamps`.
- Create `Banner` model.
- Add `TracksAdminActivity` trait to the model.

### Step 2: Seeder
- Create `BannerSeeder` to populate the 6 slots with current static asset paths.
- This ensures the site doesn't break after migration.

### Step 3: Admin Backend
- Create `app/Http/Requests/Admin/UpdateBannerRequest.php`.
- Create `app/Services/BannerService.php`.
- Create `app/Http/Controllers/Admin/BannerController.php`.
- Create views in `resources/views/admin/banners/`:
    - `index.blade.php`: List the 6 banner slots.
    - `edit.blade.php`: Edit a specific banner slot with size instructions.

### Step 4: Frontend Integration
- Update `HelperClass.php` to add `getBanner($slug)` method.
- Update `resources/views/client/partials/banner_1.blade.php`.
- Update `resources/views/client/partials/banner_2.blade.php`.
- Update `resources/views/client/cart.blade.php`.
- Update `resources/views/client/structure/partials/header.blade.php`.

### Step 5: Finalize
- Run `vendor/bin/pint --dirty`.
- Run `php artisan optimize`.
- Update `PROJECT_DOCUMENTATION.md`.

## 3. Verification Criteria
- [x] Admin can see the 6 banner slots in the admin panel.
- [x] Admin can upload new images for each slot.
- [x] Size recommendations are clearly visible in the edit form.
- [x] Changes reflect instantly on the homepage and cart page.
- [x] Default images are used if no upload exists (handled by seeder).
