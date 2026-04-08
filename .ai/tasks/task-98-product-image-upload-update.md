# Task 98: Update Product Image Upload Logic

## Requirement
Implement REQ-135: Update image upload logic to allow max 2MB per image, limit gallery uploads to 5 images at a time, and introduce a dedicated "Primary Image" field separate from the "Gallery Images" field.

## Implementation Steps

### 1. Update ProductRequest
- Change validation rule for `images` to `gallery_images`.
- Add validation for `primary_image`.
- Update max size from `600KB` to `2048KB` (2MB).
- Limit `gallery_images` to a maximum of 5 items.
- Ensure allowed mimes are correct.

### 2. Update ProductService
- Modify `storeProduct` to handle `primary_image` and `gallery_images`.
- Modify `updateProduct` to handle `primary_image` and `gallery_images`.
- Ensure that if a new `primary_image` is uploaded during update, the old primary status is removed (and ideally the file is replaced or added as a new primary).
- Update `createProductImage` helper if needed.

### 3. Update Product Form View (`form.blade.php`)
- Add a dedicated file input for "Primary Image".
- Update the existing gallery upload section to use the name `gallery_images[]`.
- Update the descriptive text for size limits and count limits.
- Update FilePond configuration if it's used to enforce the 5-image limit.

### 4. Verification
- Test creating a product with a primary image and 5 gallery images.
- Test updating a product by changing the primary image.
- Test updating a product by adding 5 more gallery images.
- Verify that exceeding 2MB or 5 gallery images triggers validation errors.

## Verification Criteria
- Primary image is correctly identified and displayed.
- Gallery images are correctly saved and linked.
- Max size 2MB is enforced.
- Max 5 images per upload for gallery is enforced.
- Old primary image is correctly handled when a new one is uploaded.
