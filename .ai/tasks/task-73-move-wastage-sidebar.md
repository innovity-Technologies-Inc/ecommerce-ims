# Task 73: Move Wastage section from Returns to Inventory in the Admin Sidebar

## Description
Move the "Wastages" menu item from the "Returns" section to the "Inventory" section in the admin sidebar for better categorization.

## Requirements
- **REQ-108:** Move Wastage Sidebar Menu: Move the "Wastages" menu item from the "Returns" section to the "Inventory" section in the admin sidebar for better categorization.

## Implementation Steps
1. **Modify Sidebar View:**
    - Edit `resources/views/admin/structure/partials/sidebar.blade.php`.
    - Remove the "Wastages" link from the "Returns" collapse menu.
    - Add the "Wastages" link to the "Inventory" collapse menu.

## Verification Criteria
- [x] "Wastages" menu item is no longer visible under "Returns".
- [x] "Wastages" menu item is now visible under "Inventory".
- [x] Clicking the "Wastages" link in the new location still leads to the correct route (`admin.returns.wastages`).
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.
