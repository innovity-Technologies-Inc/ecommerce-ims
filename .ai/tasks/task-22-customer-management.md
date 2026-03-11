# Task 22: Customer Management Module (Admin)

Implement a comprehensive customer management module in the admin panel to manage registered users.

## 1. Requirement Logging
- [x] **REQ-38:** Customer Management Module (Admin panel for managing authenticated users: list, profile, purchase history, status toggle, and deletion).

## 2. Implementation Steps

### Phase 1: Database & Model
- [x] Create migration to add `status` (active/inactive) to `users` table.
- [x] Update `User` model with `status` field and `casts`.

### Phase 2: Logic (Service Layer)
- [x] Create `CustomerManagementService` in `app/Services`.
- [x] Implement `getAllCustomers()` with pagination.
- [x] Implement `getCustomerWithOrders(int $id)` to fetch profile and purchase history.
- [x] Implement `toggleCustomerStatus(int $id)` method.
- [x] Implement `deleteCustomer(int $id)` method.

### Phase 3: Controller & Request
- [x] Create `app/Http/Controllers/Admin/CustomerController.php` (if not exists or use a new one to avoid confusion with client side).
- [x] Create `UpdateCustomerStatusRequest` for validation.
- [x] Register admin routes for customer management.

### Phase 4: Admin UI
- [x] Create `resources/views/admin/customers/index.blade.php` (List view with status toggle).
- [x] Create `resources/views/admin/customers/show.blade.php` (Profile & Purchase History).
- [x] Add "Customers" section to Admin sidebar.

### Phase 5: Verification & Documentation
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Verify with existing user data (Seeders).
- [x] Update `PROJECT_DOCUMENTATION.md` with detailed "What" and "How".

## 3. Verification Criteria
- [x] Admin can view a paginated list of all registered customers.
- [x] Admin can view a specific customer's profile details.
- [x] Admin can view a customer's full purchase history.
- [x] Admin can toggle customer status between Active and Inactive.
- [x] Inactive customers are prevented from logging in via `LoginRequest` guardrail.
- [x] Admin can delete a customer account.
