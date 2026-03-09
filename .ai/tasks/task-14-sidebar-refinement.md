# Task 14: Sidebar UI Refinement

Refine the admin sidebar to only show implemented modules and link the Users section to Admin management.

## 1. Requirement
- **REQ-29:** Sidebar UI Refinement (Hide unlinked menus, link Users to Admin CRUD).

## 2. Implementation Steps
- [x] **Link Dashboard**: Update Dashboard link to `route('admin.dashboard')`.
- [x] **Hide Unlinked Menus**: Remove/Hide Inventory, Orders, Purchases, Attributes, Invoices, Roles, Permissions, Customers, Sellers, Coupons, Reviews, Chat, Email, Calendar, Todo, Help Center, FAQs, Privacy Policy, Pages, Authentication, Widgets, and Base UI/Advanced UI/Charts/Forms/Tables/Icons/Maps components.
- [x] **Refactor Users Section**: 
    - Rename "Admins" (standalone link) to "Users" (dropdown).
    - Link "List" to `route('admin.index')`.
    - Link "Create" to `route('admin.create')`.
- [x] **Refactor Settings Section**: Group General and Mail settings under a single "Settings" dropdown.

## 3. Verification Criteria
- [x] Verify sidebar only shows: Dashboard, Products, Category, Brands, Homepage, Users, and Settings.
- [x] Verify all links point to valid routes.
