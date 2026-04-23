# Task-228: Always Show Clock In/Out Button

## Requirement
The Clock In/Out button in the admin header should always be visible to logged-in administrators, regardless of the `is_time_tracking` setting in their profile.

## Implementation Steps
1. **Model Update:** Add `is_clocked_in` to `Admin` model `$fillable` and `casts` for completeness.
2. **View Update:** Remove the `@if(auth('admin')->user()->is_time_tracking)` check around the Clock In/Out button in `resources/views/admin/structure/partials/header.blade.php`.
3. **Verification:**
    - Log in as an admin with `is_time_tracking` disabled.
    - Verify that the "Clock In" button is still visible in the header.
    - Verify that clicking it works correctly.
    - Run `php artisan optimize` to clear caches.
4. **Documentation:** Update `PROJECT_DOCUMENTATION.md` and `USER_GUIDE.md`.

## Verification Criteria
- [ ] Clock In/Out button is visible for all admins.
- [ ] Logic for clocking in/out remains functional.
- [ ] `Admin` model correctly handles `is_clocked_in` attribute.
