# Task 213: Customer Profile Image Upload

Add the ability for customers to upload and update their profile images.

## Requirement Reference
- **REQ-213:** Customer Profile Image Upload.

## Implementation Steps

### 1. Database Update
- **Action:** Create a migration to add an `image` column (string, nullable) to the `users` table.
- **Command:** `php artisan make:migration add_image_to_users_table --table=users`

### 2. Backend Implementation
- **Form Request:** Create `app/Http/Requests/Client/UpdateProfileImageRequest.php` to validate the uploaded image.
- **Service Layer:** Update `app/Services/CustomerProfileService.php` to include an `updateAvatar` method that handles file storage and database update.
- **Controller:** Update `app/Http/Controllers/CustomerController.php` to handle the avatar update request.

### 3. UI Update
- **File:** `resources/views/client/auth/account_info.blade.php`
- **Changes:**
    - Wrap the `.account-avatar` in a container with a relative position.
    - Add an edit icon (`solar:camera-bold-duotone`) that triggers a Bootstrap modal.
    - Implement the `changeAvatarModal` with a file input field.

### 4. Verification
- Verify the migration runs successfully.
- Verify that a customer can upload an image and it correctly displays in the sidebar.
- Verify that uploading a new image deletes the old one from storage.

## Documentation Update
- Update `PROJECT_DOCUMENTATION.md` to reflect the profile image upload capability for customers.
