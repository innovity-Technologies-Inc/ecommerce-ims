# Task 58: Admin Avatar UI Standardization

Ensure all admin avatars in the system have a consistent, circular shape and fixed size using CSS.

## Requirements
- [x] **Global Styling:** Add global CSS for `avatar-*` classes to ensure `aspect-ratio: 1/1` and `object-fit: cover` when used with `rounded-circle`.
- [x] **Navbar:** Update the admin navbar to use standard avatar classes and ensure circular display.
- [x] **Index Table:** Ensure the admin list table uses standardized classes.
- [x] **Form Preview:** Update the profile edit form image preview to be circular and fixed size.

## Implementation Steps

1. **Global CSS:**
    - Updated `resources/views/admin/structure/master.blade.php` with global avatar size and shape rules. [DONE]

2. **Navbar Update:**
    - Updated `resources/views/admin/structure/partials/header.blade.php` to use `avatar-sm` and `rounded-circle`. [DONE]

3. **Index Table Update:**
    - Verified `resources/views/admin/users/partials/table.blade.php` uses `avatar-sm` and `rounded-circle`. [DONE]

4. **Form Preview Update:**
    - Updated `resources/views/admin/users/forms.blade.php` to use `avatar-xl` and `rounded-circle` for the profile image preview. [DONE]

5. **Cleanup:**
    - Ran `php artisan optimize`. [DONE]
