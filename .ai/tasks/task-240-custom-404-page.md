# Task 240: Custom 404 (Not Found) View

Replace the default Laravel 404 error page with beautifully designed, context-aware views.

## 1. Requirement Details
- **Requirement ID:** REQ-240
- **Focus:** UI/UX & Navigation Recovery.
- **Description:** When a user accesses a non-existent URL, show a professional "Page Not Found" screen. The design should match the project's aesthetics and provide clear navigation back to the Admin Dashboard or the Client Shop depending on where the user was.

## 2. Implementation Steps
1. Create `resources/views/errors/admin-404.blade.php`:
    - Dark theme, Emerald glow.
    - Large "404" typography.
    - Text: "The administrative page you are looking for does not exist."
    - Button: "Back to Dashboard".
2. Create `resources/views/errors/404.blade.php`:
    - Logic to include `admin-404` if the URL starts with `/admin`.
    - Light theme, Shop aesthetic (similar to the 419 client view).
    - Text: "We couldn't find the page you're looking for."
    - Button: "Return to Shop".
3. Ensure responsiveness and typographical synchronization with the 419 pages.

## 3. Verification Criteria
- [x] Verify that the custom 404 page is displayed for non-existent routes.
- [x] Verify the Admin version is shown for `/admin/anything-fake`.
- [x] Verify the Client version is shown for `/anything-fake`.
- [x] Run `./vendor/bin/pint --dirty` to maintain project styling.

## 4. Documentation Update
- [x] Update `PROJECT_DOCUMENTATION.md` under the UI/UX section to mention custom 404 handling.
