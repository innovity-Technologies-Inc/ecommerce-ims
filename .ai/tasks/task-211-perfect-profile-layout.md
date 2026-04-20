# Task 211: Perfect Profile Desktop/Mobile Layout

Refine the CSS Grid strategy to support a perfect sidebar on desktop and an interleaved accordion on mobile.

## Requirement Reference
- **REQ-211:** Perfect Profile Desktop/Mobile Layout.

## Implementation Steps

### 1. HTML Restructuring
- **File:** `resources/views/client/auth/account_info.blade.php`
- **Action:** 
    - Move `.profile-user-info` and the nav items into a flat list inside `.profile-container`.
    - Add a `.profile-sidebar-visual` div that acts as the white background card on desktop.

### 2. CSS Grid Refinement
- **Mobile:**
    - `.profile-container` is a simple block/flex container.
    - `.profile-sidebar-visual` is hidden.
    - Forms follow buttons naturally.
- **Desktop (@media 992px):**
    - `.profile-container` -> `display: grid; grid-template-columns: 350px 1fr;`.
    - `.profile-sidebar-visual` -> `display: block; grid-column: 1; grid-row: 1 / span 20;` (to provide the card look).
    - `.profile-user-info` -> `grid-column: 1; z-index: 2;`.
    - `.nav-link` buttons -> `grid-column: 1; z-index: 2;`.
    - `.tab-pane.active` -> `grid-column: 2; grid-row: 1 / span 20;` (to appear on the right).

### 3. Verification
- **Desktop:** Confirm the white card contains the user info and buttons, and forms appear on the right.
- **Mobile:** Confirm the forms expand below each button.
- **Functional:** Ensure all tabs/forms still work.

## Documentation Update
- Note the final responsive grid implementation details in `PROJECT_DOCUMENTATION.md`.
