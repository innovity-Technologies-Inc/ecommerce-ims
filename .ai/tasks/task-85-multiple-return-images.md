# Task 85: Multiple Image Uploads for Returns (REQ-123)

Enable clients to upload multiple images when requesting a return for better proof of condition.

## Requirements
- Support multiple image selection in the client-side return form.
- Create `return_images` table to store multiple paths.
- Update `ReturnRequest` model to link with `return_images`.
- Display all uploaded images in the admin return request details view.

## Implementation Steps

### 1. Database Layer
- [ ] Create migration for `return_images` table:
    - `id`
    - `return_id` (foreign key)
    - `image_path`
    - `timestamps`
- [ ] Keep the `image` column in `returns` table for the "Primary" or "First" image (optional, or just migrate all to the new table). *Decision: I will migrate everything to the new table and eventually nullable the old column if preferred, but for now I will just use the new table for all new uploads.*

### 2. Model Updates
- [ ] Create `ReturnImage` model.
- [ ] Update `ReturnRequest` model with `returnImages()` HasMany relationship.

### 3. Service Layer Updates (`ReturnService.php`)
- [ ] Update `storeReturnRequest`:
    - Handle multiple files from `$request->file('images')`.
    - Loop and store each image using `HelperClass::file_upload()`.
    - Create `ReturnImage` records.

### 4. UI Layer Updates
- [ ] **Client Side (`resources/views/client/returns/index.blade.php`):**
    - Change input to `<input type="file" name="images[]" multiple>`.
    - Update JS to handle multiple files in FormData.
- [ ] **Admin Side (`resources/views/admin/returns/show_request.blade.php`):**
    - Loop through `returnImages` and display them in a gallery/grid format.

### 5. Verification
- [ ] Run migrations.
- [ ] Submit a return request with 3 images.
- [ ] Check `return_images` table.
- [ ] View the request in Admin Panel and verify all 3 images are visible.
- [ ] Run `php artisan optimize`.
- [ ] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- [ ] Clients can select and upload multiple images.
- [ ] All images are stored and linked correctly in the database.
- [ ] Admin can see all images in the request details view.
