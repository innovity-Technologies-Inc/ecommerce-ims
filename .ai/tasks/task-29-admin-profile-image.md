# Task: Admin Profile Image Implementation

Admin users (administrators) should have a profile image that they can upload and update in the admin panel.

## Implementation Steps

1.  **Migration**: Add `image` column to the `admins` table.
2.  **Model**: Update `Admin` model to include `image` in `$fillable`.
3.  **Requests**: Update `StoreAdminRequest` and `UpdateAdminRequest` to include validation for `image`.
4.  **Service**: Update `AdminService` to handle image upload/deletion using `HelperClass`.
5.  **View (Forms)**: Update the admin creation/edit form (`admin.users.forms`) to include an image upload field.
6.  **View (Index)**: Update the admin list view (`admin.users.index`) to display the profile image.
7.  **Header**: Update the admin panel header to display the authenticated admin's profile image.

## Verification Criteria

- [x] Migration adds the `image` column successfully.
- [x] Admin creation form allows uploading an image.
- [x] Admin edit form shows the current image and allows updating it.
- [x] Old images are deleted when a new one is uploaded.
- [x] Admin list view displays the profile image (or a placeholder if none).
- [x] Header shows the profile image of the logged-in admin.
- [x] Verification script/test confirms image upload functionality.
