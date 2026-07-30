# Task 244: Unified Logo Layout and Vertical Centering

Ensure both the admin panel sidebar logo and the client-side navbar logo maintain their aspect ratios and stay correctly fitted within their containing wrappers. Additionally, ensure the client-side logo and menu are vertically centered in the navbar.

## Requirement Reference
- **REQ-244:** Unified Logo Layout and Vertical Centering.

## Implementation Steps

### 1. Admin Master Style Update
- **File:** `resources/views/admin/structure/master.blade.php`
- **Action:** Add CSS overrides to style `.main-nav .logo-box` and `.main-nav .logo-box .logo-lg` specifically to allow full width and aspect ratio preservation.
- **Changes:**
    - Override `.main-nav .logo-box` to have `padding: 0 !important;` (removes the default side padding), `display: flex !important;`, `align-items: center !important;`, `justify-content: center !important;`, and `height: var(--bs-topbar-height) !important;`.
    - Style the links `a` inside `.logo-box` to occupy `width: 100%;` and `height: 100%;` with flex alignment based on the active theme.
    - Style `.logo-lg` inside `.logo-box` to have `width: 100% !important;`, `height: auto !important;`, `max-height: calc(var(--bs-topbar-height) - 10px) !important;`, and `object-fit: contain !important;`.

### 2. Admin Sidebar View Update
- **File:** `resources/views/admin/structure/partials/sidebar.blade.php`
- **Action:** Adjust the inline styles of `logo-lg` to match and support the full-width layout.
- **Changes:**
    - Remove `style="height: 50px;"` on the `logo-lg` elements.

### 3. Client Header View Update (Layout & Alignment)
- **File:** `resources/views/client/structure/partials/header.blade.php`
- **Action:** Update both the desktop and mobile header logo inline styles to prevent overflow and maintain aspect ratio inside their respective grid containers. Make the header navigation bar align-items centered to align the logo and menu vertically.
- **Changes:**
    - **Desktop Logo (Line 117):** Change the inline style to `style="max-width: 100%; max-height: 50px; width: auto; height: auto; object-fit: contain;"`.
    - **Mobile Logo (Line 234):** Change the inline style to `style="max-width: 100%; max-height: 40px; width: auto; height: auto; display: block; object-fit: contain;"`.
    - **Row Alignment (Line 113):** Add `align-items-center` to the `.row` layout container.
    - **Logo Container (Line 116):** Set margin of `.logo` to 0 (`m-0`).
    - **Column Alignment (Line 122):** Convert the navigation column (`col-md-10`) into a flexbox container with `d-flex align-items-center justify-content-between` to center navigation elements and cart items horizontally and vertically.
    - **Main Navigation (Line 124):** Remove floats and margins by setting classes to `main-navigation m-0 float-none`.
    - **Header Account Area (Line 190):** Remove margin by setting class to `header_account_area m-0 align-items-center`.

## Verification
- **Admin Desktop Sidebar:** Confirm the logo spans the full width of the sidebar and scales nicely without losing aspect ratio.
- **Admin Condensed Sidebar:** Confirm the small favicon/logo remains correctly centered and sized.
- **Client Desktop Navbar:** Confirm the logo fits inside `col-md-2` and scales correctly. Verify that the logo and the menu links are perfectly aligned vertically centered relative to each other.
- **Client Mobile Navbar:** Confirm the logo fits inside `col-6` and does not overflow or stretch.
