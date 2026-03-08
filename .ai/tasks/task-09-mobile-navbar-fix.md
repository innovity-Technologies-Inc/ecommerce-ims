# Task: Mobile Navbar Alignment (REQ-24)

## Status: Completed [x]

## Implementation Plan
1. **Analyze Header Structure:**
   - [x] Identified the padding and grid conflict in `resources/views/client/structure/partials/header.blade.php`.
   - [x] Removed `col-md-x` classes to use raw `col-x` for stable mobile grid.

2. **Frontend Adjustment:**
   - [x] Adjusted the column grid for mobile: Menu (3), Logo (6), Icons (3).
   - [x] Applied `p-0` and `m-0` to the logo container to ensure it stays centered.
   - [x] Simplified the cart display on mobile by removing the amount-tag for more icon space.

3. **Verification:**
   - [x] Verified centering on mobile view.
   - [x] Verified that user and cart icons are correctly aligned on the right.
