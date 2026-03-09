# Task 13: Admin Module Architectural Refactoring

Refactor the Admin management module to strictly follow the "Service Layer", "Thin Controller", and "Form Request" patterns.

## 1. Requirement
- **REQ-28:** Admin Management Architectural Refactoring (Refactor Admin CRUD to Service Layer and Form Request patterns).

## 2. Implementation Steps
- [x] **Form Requests**:
    - Create `StoreAdminRequest` for admin creation.
    - Create `UpdateAdminRequest` for admin updates.
- [x] **AdminService**:
    - Implement `getAllAdmins()` with pagination.
    - Implement `storeAdmin(array $data)`.
    - Implement `updateAdmin(int $id, array $data)`.
    - Implement `deleteAdmin(int $id)`.
- [x] **AdminController**:
    - Inject `AdminService`.
    - Refactor all CRUD methods to use the Service and Form Requests.
- [x] **Verification**:
    - Verify admin creation, update, and deletion still work correctly.

## 3. Verification Criteria
- [x] Manual verification of Admin CRUD in the dashboard.
- [x] Ensure no business logic remains in `AdminController`.
