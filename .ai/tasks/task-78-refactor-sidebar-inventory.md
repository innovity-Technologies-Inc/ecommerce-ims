# Task 78: Refactor Admin Sidebar - Inventory Sections

## Requirement
REQ-115: Refactor Admin Sidebar to elevate Inventory submenus to top-level menu items for better accessibility.

## Implementation Details
1.  **Modified `resources/views/admin/structure/partials/sidebar.blade.php`**:
    *   Removed the collapsible "Inventory" menu.
    *   Elevated "Stock", "Batch Tracking", "Damaged Products", and "Wastages" to standalone top-level items under the "Inventory" title.
    *   Elevated "Stock Adjustment" and "Supplier RMA" to standalone top-level items with their respective `@can` checks.
    *   Assigned unique Solar Bold Duotone icons to each new top-level item to improve visual differentiation.

## Verification Criteria
- [x] "Inventory" section no longer uses a collapsible menu.
- [x] All previous submenus are now direct links in the sidebar.
- [x] Icons are correctly displayed and consistent with the theme.
- [x] Permissions are still correctly enforced for "Stock Adjustment" and "Supplier RMA".
- [x] Documentation updated in `PROJECT_DOCUMENTATION.md`.
