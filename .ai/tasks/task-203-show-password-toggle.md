# Task 203: Show Password Toggle Implementation

Implement a "Show Password" checkbox on all password-related forms in both Admin and Client interfaces.

## Requirement Reference
- **REQ-203:** Show Password Toggle.

## Implementation Steps

### 1. Global JavaScript Handler
- Create/Update a global JavaScript handler (e.g., in a common JS file or master layout) that listens for changes on checkboxes with the class `toggle-password-visibility`.
- Logic: When the checkbox is checked, find all `input[type="password"]` fields within the same form (or global scope) and change their type to `text`. When unchecked, change them back to `password`.

### 2. Admin Interface Updates
- **Admin Login:** `resources/views/admin/auth/login.blade.php` (or similar).
- **Admin Profile:** `resources/views/admin/profile/show.blade.php`.
- **Admin Create/Edit:** Any other forms with passwords.

### 3. Client Interface Updates
- **Client Login:** `resources/views/client/auth/login.blade.php`.
- **Client Register:** `resources/views/client/auth/register.blade.php`.
- **Client Account Info:** `resources/views/client/auth/account_info.blade.php`.
- **Client Reset Password:** `resources/views/client/auth/reset-password.blade.php`.

### 4. Verification
- Verify the toggle works for all types of password fields (Current, New, Confirm).
- Ensure the toggle only affects the relevant form's inputs if multiple forms exist.
- Verify styling consistency across Admin and Client.

## Documentation Update
- Update `PROJECT_DOCUMENTATION.md` to include the global password visibility toggle standard.
