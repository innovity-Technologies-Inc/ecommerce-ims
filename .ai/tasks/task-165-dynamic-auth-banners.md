# Task-165: Dynamic Auth Banners

## Objective
Enable administrators to manage the banners for the login and registration pages from the admin panel.

## Implementation Steps
- [x] Create migration `add_auth_banners_to_general_settings_table`.
- [x] Update `GeneralSetting.php` model with `login_banner` and `register_banner` in `$fillable`.
- [x] Update `resources/views/admin/settings/general.blade.php` to include file upload fields for banners.
- [x] Update `app/Services/SettingsService.php` to handle file uploads and deletions for the new banners.
- [x] Update `app/Http/Requests/Admin/GeneralSettingRequest.php` with validation rules.
- [x] Update `resources/views/client/auth/login.blade.php` to show dynamic banner.
- [x] Update `resources/views/client/auth/register.blade.php` to show dynamic banner.
- [x] Update `USER_GUIDE.md` and `PROJECT_DOCUMENTATION.md`.
- [x] Run `php artisan optimize`.

## Verification
- [x] Upload a new banner in General Settings and verify it appears on the Login page.
- [x] Verify the Registration page shows its specific banner.
- [x] Verify fallback design works if banners are deleted or not uploaded.
