# Task: Multi-Guard Authentication (REQ-01, REQ-02, REQ-03, REQ-04)

## Status: Completed [x]

## Implementation Details
1. **Multi-Guard Setup:**
   - [x] Defined `admin` guard in `config/auth.php`.
   - [x] Created `App\Models\Admin` model with the `Authenticatable` trait.
   - [x] Configured separate authentication providers for `User` and `Admin`.

2. **Admin Auth Flows:**
   - [x] Implemented dedicated admin login routes and controller.
   - [x] Created admin-specific login view (`resources/views/admin/auth/login.blade.php`).
   - [x] Protected admin routes using `auth:admin` middleware.

3. **Client Auth Flows:**
   - [x] Used Laravel Breeze for standard user registration and login.
   - [x] Customized client login and registration views to match the project's styling.

4. **Profile Management:**
   - [x] Created `ProfileController` and `ProfileUpdateRequest` for updating user and admin info.
   - [x] Added functionality for updating passwords securely.

## Verification
- [x] Verified that Admin and User sessions do not overlap.
- [x] Confirmed that unauthorized access to the admin dashboard is redirected to the admin login page.
