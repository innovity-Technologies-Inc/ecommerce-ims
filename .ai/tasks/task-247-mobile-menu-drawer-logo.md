# Task 247: Mobile Menu Drawer Logo (REQ-247)

## Requirements
1. Add a full-width brand logo to the top of the mobile navigation offcanvas drawer (`#offcanvas-mobile-menu`), ensuring it spans the drawer width cleanly while maintaining its aspect ratio.
2. Space the logo container down to avoid overlap with the close button of the offcanvas drawer.

## Steps
1. **Insert Logo Markup:**
   - Locate the `#offcanvas-mobile-menu` element in `resources/views/client/structure/partials/header.blade.php`.
   - Add a new container `<div class="mobile-menu-logo text-center border-bottom pb-3 mb-3" style="padding-top: 50px;">` right under the close button.
   - Render the light logo (with fallback) styled with `width: 100%; height: auto; max-height: 80px; object-fit: contain;`.
2. **Verification:**
   - Refresh the view cache using `php artisan optimize`.
   - Toggle the mobile menu drawer on smaller screen sizes and verify the logo is centered, displays at full width, and has proper padding relative to the close button.
