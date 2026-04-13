# Task 170: Dedicated Admin Profile Page (REQ-170)

Implement a dedicated profile view and edit page for the logged-in administrator.

## 1. Database Schema
- No changes required. Uses the existing `admins` table.

## 2. Implementation Steps

### Step 1: Form Request for Validation
- Create `App\Http\Requests\Admin\AdminProfileUpdateRequest` using `php artisan make:request Admin/AdminProfileUpdateRequest`.
- Validation Rules:
    - `name`: required, string, max:255.
    - `email`: required, email, max:255, unique:admins,email,{id}.
    - `avatar`: nullable, image, max:2048.
    - `password`: nullable, string, min:8, confirmed.

### Step 2: Service Layer
- Create `App\Services\AdminProfileService`.
- Implement `updateProfile(int $id, array $data): bool`.
- Logic:
    - Handle avatar upload using `HelperClass::file_upload`.
    - If password provided, hash it.
    - Update the admin record.

### Step 3: Controller
- Create `App\Http\Controllers\Admin\AdminProfileController`.
- Methods:
    - `show()`: Returns the profile view (`admin.profile.show`).
    - `edit()`: Returns the edit view (`admin.profile.edit`).
    - `update(AdminProfileUpdateRequest $request)`: Calls the service and redirects.

### Step 4: UI / Views
- **Show Page (`resources/views/admin/profile/show.blade.php`):**
    - Display Name, Email, Avatar, and Role (Read-only).
    - "Edit Profile" button linking to the edit page.
    - Extend `admin.structure.app`.
- **Edit Page (`resources/views/admin/profile/edit.blade.php`):**
    - Form with fields: Name, Email, Avatar Upload, Password, and Password Confirmation.
    - **MANDATORY:** No Role selection field.
    - Use Bootstrap 5 and standard JS/jQuery.
    - Extend `admin.structure.app`.

### Step 5: Routing
- Add routes in `routes/web.php` within the `admin` middleware group:
    - `Route::get('/profile', [AdminProfileController::class, 'show'])->name('admin.profile.show');`
    - `Route::get('/profile/edit', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');`
    - `Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');`

### Step 6: Navbar/Sidebar Integration
- Update the admin navbar/dropdown to link to the new profile page.

## 3. Verification Criteria
- [x] Admin can view their own profile details.
- [x] Admin can update their name, email, and avatar.
- [x] Admin can change their password.
- [x] **Role selection is NOT present in the edit form.**
- [x] Validation prevents duplicate emails (except for the current user).
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.

## 4. Documentation Update
- Update `PROJECT_DOCUMENTATION.md` with the new Admin Profile module details.
