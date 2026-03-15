# Task 43: Social Login Icon Update

Replace the text-heavy Google login button on the client login page with a modern icon-dominant or icon-only layout.

## Requirements
- Update `resources/views/client/auth/login.blade.php` and `resources/views/client/auth/register.blade.php` to replace the "Google" text button with a modern, multi-colored Google "G" SVG logo.
- Ensure the layout remains clean, borderless, and without a background color, aligned with modern minimal design standards.
- Maintain functionality (the link to `route('auth.google')`).

## Implementation Steps
1. **Identify Target Files:** `resources/views/client/auth/login.blade.php` and `resources/views/client/auth/register.blade.php`.
2. **Modify UI:** Replace the existing button structure with a more compact SVG-based version. Ensure no background or border classes are applied.
3. **Verification:** Check the login and registration pages to ensure the logo is displayed correctly, the scaling hover effect works, and the link functions.

## Verification Criteria
- [x] Google login button is replaced by a multi-colored Google "G" SVG logo.
- [x] The button has no background color and no border.
- [x] The icon is clickable and redirects to the Google auth route.
- [x] The UI remains responsive and visually appealing.
- [x] `./vendor/bin/pint --dirty` is run.
- [x] `php artisan optimize` is run.
