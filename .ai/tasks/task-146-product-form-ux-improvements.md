# Task-146: Product Form UX Improvements

## Objective
Improve the user experience of the product creation and edit form by providing more specific validation error messages for image uploads and adding a helpful formatting note for the SummerNote editor.

## Implementation Steps

### 1. Update Validation Messages
- **File:** `app/Http/Requests/Admin/ProductRequest.php`
- **Change:** Refine the custom error messages in the `messages()` method.
    - Change `primary_image.max` to include the specific limit (2MB).
    - Change `gallery_images.*.max` to be more descriptive than "Each gallery image must be smaller than 2MB."

### 2. Add Formatting Guidance
- **File:** `resources/views/admin/products/form.blade.php`
- **Change:** Add a `<p class="text-muted extra-small mt-1">` or similar note under the "Main Description" label.
    - Text: "Note: For optimal formatting, it is recommended to copy and paste content from Google Docs or Microsoft Word."

## Verification
- Attempt to upload an image > 2MB and verify the specific error message.
- Verify the guidance note is visible under the Main Description field.
- Run `php artisan optimize` and `./vendor/bin/pint --dirty`.
