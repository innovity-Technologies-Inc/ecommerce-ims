# Task 60: Inventory Management Onboarding (Warehouses & Suppliers)

## Requirements
- **REQ-85:** Create Locations (Warehouses): name, location.
- **REQ-86:** Onboard Vendors (Suppliers): name, email, mobile, address.

## Implementation Steps

### 1. Database & Models
- [x] Create migration for `warehouses` table (id, name, location, timestamps).
- [x] Create `Warehouse` model.
- [x] Create migration for `suppliers` table (id, name, email, mobile, address, timestamps).
- [x] Create `Supplier` model.

### 2. Service Layer
- [x] Create `InventoryService` (or separate `WarehouseService` and `SupplierService` if logic grows, but for now `InventoryService` can handle both basic CRUDs).
- [x] Implement CRUD logic for Warehouses.
- [x] Implement CRUD logic for Suppliers.

### 3. Validation (Form Requests)
- [x] Create `WarehouseRequest` for store/update.
- [x] Create `SupplierRequest` for store/update.

### 4. Controllers
- [x] Create `WarehouseController` (Admin).
- [x] Create `SupplierController` (Admin).

### 5. Views (Admin Panel)
- [x] Create Warehouse index, create, edit views.
- [x] Create Supplier index, create, edit views.
- [x] Update Sidebar with new "Inventory" menu and submenus for Warehouses and Suppliers.

### 6. Verification
- [x] Create Seeders for Warehouses and Suppliers.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- Admin can create, read, update, and delete warehouses.
- Admin can create, read, update, and delete suppliers.
- All views follow the Bootstrap 5 design standards.
- Logic is strictly in the Service layer.
- Form requests are used for validation.
