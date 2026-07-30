# Task 246: Mobile Header Logo Sizing (REQ-246)

## Requirements
1. Increase the maximum height limit of the logo image displayed in the mobile header (navbar) from `40px` to `55px`.

## Steps
1. **Modify Mobile Logo Style:**
   - Locate the mobile navigation header block in `resources/views/client/structure/partials/header.blade.php`.
   - Update the inline `style` attribute on the mobile logo `img` element to change `max-height: 40px;` to `max-height: 55px;`.
2. **Verification:**
   - Refresh the view cache using `php artisan optimize`.
   - Verify that the mobile logo scales up nicely to `55px` height on smaller screen widths.
