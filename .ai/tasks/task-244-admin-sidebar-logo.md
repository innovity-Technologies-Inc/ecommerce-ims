# Task 244: Admin Sidebar Logo Layout

Ensure the admin panel sidebar logo spans the full width of the sidebar while maintaining its aspect ratio.

## Requirement Reference
- **REQ-244:** Admin Sidebar Logo Layout.

## Implementation Steps

### 1. Master Style Update
- **File:** `resources/views/admin/structure/master.blade.php`
- **Action:** Add CSS overrides to style `.main-nav .logo-box` and `.main-nav .logo-box .logo-lg` specifically to allow full width and aspect ratio preservation.
- **Changes:**
    - Override `.main-nav .logo-box` to have `padding: 0 !important;` (removes the default side padding), `display: flex !important;`, `align-items: center !important;`, `justify-content: center !important;`, and `height: var(--bs-topbar-height) !important;`.
    - Style the links `a` inside `.logo-box` to occupy `width: 100%;` and `height: 100%;` with flex centring.
    - Style `.logo-lg` inside `.logo-box` to have `width: 100% !important;`, `height: auto !important;`, `max-height: calc(var(--bs-topbar-height) - 10px) !important;`, and `object-fit: contain !important;`.

### 2. Sidebar View Update
- **File:** `resources/views/admin/structure/partials/sidebar.blade.php`
- **Action:** Adjust the inline styles of `logo-lg` to match and support the full-width layout.
- **Changes:**
    - Replace `style="height: 50px;"` on the `logo-lg` elements with a more flexible inline style or ensure it respects the stylesheet classes.

## Verification
- **Desktop Sidebar:** Confirm the logo spans the full width of the sidebar and scales nicely without losing aspect ratio.
- **Condensed Sidebar:** Confirm the small favicon/logo remains correctly centered and sized.
