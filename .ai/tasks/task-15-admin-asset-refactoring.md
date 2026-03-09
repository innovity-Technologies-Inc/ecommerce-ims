# Task 15: Admin Asset Path Refactoring

Rename the public/admin directory and update all its references to resolve route conflicts.

## 1. Requirement
- **REQ-31:** Admin Route Conflict Resolution (Rename public/admin to public/admin_assets to avoid 403 Forbidden errors).

## 2. Implementation Steps
- [x] **Rename Folder**: `public/admin` -> `public/admin_assets`.
- [x] **Update References**: 
    - [x] `GeneralSettingSeeder.php`
    - [x] `product_card.blade.php`
    - [x] `login.blade.php`
    - [x] `sidebar.blade.php`
    - [x] `master.blade.php`
- [x] **Verification**: No remaining `admin/assets` strings in the project.

## 3. Verification Criteria
- [x] Accessing `/admin` redirects to `/admin/dashboard`.
- [x] All admin styles and scripts load correctly.
- [x] Logos and images from admin assets display correctly.
