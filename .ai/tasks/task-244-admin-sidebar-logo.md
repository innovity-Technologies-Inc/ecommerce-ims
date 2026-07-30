# Task 244: Unified Logo Layout and Aspect Ratio

Ensure both the admin panel sidebar logo and the client-side navbar logo maintain their aspect ratios and stay correctly fitted within their containing wrappers without overflowing or squishing.

## Requirement Reference
- **REQ-244:** Unified Logo Layout and Aspect Ratio.

## Implementation Steps

### 1. Admin Master Style Update
- **File:** `resources/views/admin/structure/master.blade.php`
- **Action:** Add CSS overrides to style `.main-nav .logo-box` and `.main-nav .logo-box .logo-lg` specifically to allow full width and aspect ratio preservation.
- **Changes:**
    - Override `.main-nav .logo-box` to have `padding: 0 !important;` (removes the default side padding), `display: flex !important;`, `align-items: center !important;`, `justify-content: center !important;`, and `height: var(--bs-topbar-height) !important;`.
    - Style the links `a` inside `.logo-box` to occupy `width: 100%;` and `height: 100%;` with flex centering.
    - Style `.logo-lg` inside `.logo-box` to have `width: 100% !important;`, `height: auto !important;`, `max-height: calc(var(--bs-topbar-height) - 10px) !important;`, and `object-fit: contain !important;`.

### 2. Admin Sidebar View Update
- **File:** `resources/views/admin/structure/partials/sidebar.blade.php`
- **Action:** Adjust the inline styles of `logo-lg` to match and support the full-width layout.
- **Changes:**
    - Remove `style="height: 50px;"` on the `logo-lg` elements.

### 3. Client Header View Update
- **File:** `resources/views/client/structure/partials/header.blade.php`
- **Action:** Update both the desktop and mobile header logo inline styles to prevent overflow and maintain aspect ratio inside their respective grid containers.
- **Changes:**
    - **Desktop Logo (Line 117):** Change the inline style to `style="max-width: 100%; max-height: 50px; width: auto; height: auto; object-fit: contain;"`.
    - **Mobile Logo (Line 234):** Change the inline style to `style="max-width: 100%; max-height: 40px; width: auto; height: auto; display: block; object-fit: contain;"`.

## Verification
- **Admin Desktop Sidebar:** Confirm the logo spans the full width of the sidebar and scales nicely without losing aspect ratio.
- **Admin Condensed Sidebar:** Confirm the small favicon/logo remains correctly centered and sized.
- **Client Desktop Navbar:** Confirm the logo fits inside `col-md-2` and scales correctly using `max-width: 100%` and `height: auto`, maintaining its design aspect ratio without overflowing or overlapping navigation menus.
- **Client Mobile Navbar:** Confirm the logo fits inside `col-6` and does not overflow or stretch on smaller mobile screen breakpoints.
