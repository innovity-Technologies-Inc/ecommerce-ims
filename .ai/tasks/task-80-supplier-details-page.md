# Task 80: Supplier Details Page Implementation

Develop a comprehensive details view for suppliers in the Admin Panel. This page will display vendor information and a list of all associated Purchase Orders (POs) with their current statuses and performance scores.

## 1. Requirement Details
- **REQ-117:** Supplier Details Page implementation.
- **Features:**
    - Display Supplier Name, Email, Mobile, and Address.
    - Show the Average Performance Score.
    - List all Purchase Orders linked to this supplier in a paginated table.
    - Each PO in the list should show: PO Number, Order Date, Status, Total Amount, and Performance Score (if delivered).

## 2. Implementation Plan

### Phase 1: Service Layer Logic
- [ ] Add a method to `InventoryService` (or `SupplierService` if it exists) to fetch a supplier with its paginated Purchase Orders.
    - Method: `getSupplierWithOrders(int $id)`

### Phase 2: Controller & Routing
- [ ] Add `show` method to `SupplierController`.
- [ ] Define the route: `admin.inventory.suppliers.show`.

### Phase 3: UI Updates (Admin Panel)
- [ ] Create `resources/views/admin/inventory/suppliers/show.blade.php`.
    - Use a 2-column layout (Summary Card + PO List Card).
    - Include a "Back" button to the index.
- [ ] Add a "View Details" button (eye icon) to the Suppliers index table in `resources/views/admin/inventory/suppliers/partials/table.blade.php`.

## 3. Verification Criteria
- [ ] Navigate to the Supplier index.
- [ ] Click the "View" button for a supplier.
- [ ] Verify supplier info is correct.
- [ ] Verify the list of POs belongs only to that supplier.
- [ ] Verify pagination works correctly for the PO list.
- [ ] Run `./vendor/bin/pint --dirty`.
- [ ] Run `php artisan optimize`.

## 4. Documentation
- [ ] Update `PROJECT_DOCUMENTATION.md` under the "Supplier Onboarding" section to include the Details view functionality.
