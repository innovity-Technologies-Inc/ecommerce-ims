# Task 53: RBAC Module Implementation (Spatie Permissions)

Implement Role-Based Access Control (RBAC) using the `spatie/laravel-permission` package for the Admin panel.

## 1. Prerequisites & Installation
- [x] Install `spatie/laravel-permission` package.
- [x] Publish migrations and config file.
- [x] Run migrations to create permission tables.

## 2. Backend Implementation (Migrations & Models)
- [x] Update `Admin` model to use `HasRoles` trait.
- [x] Create `Role` management (CRUD) in Admin Panel.
- [x] Add `image` field handling in Admin CRUD (already handled by Service).

## 3. Service Layer & Form Requests
- [x] Create `RoleService` to handle Role logic.
- [x] Update `AdminService` to handle role assignment.
- [x] Create `RoleStoreRequest` and `RoleUpdateRequest`.
- [x] Update `StoreAdminRequest` and `UpdateAdminRequest` to include `role`.

## 4. Admin-Side Implementation (Controllers & Views)
- [x] **Role Management:**
    - [x] Create `RoleController`.
    - [x] List Roles with FlexSearch.
    - [x] Create/Edit Role forms (Name only for now).
- [x] **Admin User Management Update:**
    - [x] Update Admin forms to include Role selection (dropdown).
    - [x] Update Admin forms to include Profile Image upload (already present).
    - [x] Show assigned role in Admin list table.

## 5. Seeders & Permissions
- [x] Create `RolePermissionSeeder` to create default roles (e.g., Super Admin).
- [x] Add placeholder logic for future permission seeding.

## 6. Finalization
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## 7. Verification Criteria
- [x] Admin can create, edit, and delete Roles.
- [x] Admin can assign a Role to another Admin user.
- [x] Admin can upload/update their profile image.
- [x] Roles are correctly persisted in the database.
- [x] FlexSearch works on the Roles index page.
