# Task 62: Purchase Order (PO) Module

## 1. Requirement
Implement a Purchase Order (PO) module as part of the Inventory system. Admins should be able to create POs, select multiple products/variants, align them with a supplier, and optionally notify the supplier via email.

- **REQ-88:** Purchase Order (PO) Management.
- **REQ-89:** PO Email Notification.
- **REQ-90:** PO Status Workflow (`Draft`, `Sent`, `Delivered`).
- **REQ-91:** PO Item Tracking.

## 2. Implementation Steps

### 1. Database Layer
- Create migration for `purchase_orders` table:
    - `id`
    - `po_number` (Unique string, e.g., PO-20240001)
    - `supplier_id` (Foreign key)
    - `warehouse_id` (Foreign key, optional but recommended for receiving)
    - `total_amount` (Decimal)
    - `status` (Enum: `Draft`, `Sent`, `Delivered`)
    - `notify_supplier` (Boolean)
    - `notes` (Text, nullable)
    - `created_by` (Foreign key to `admins`)
    - Timestamps
- Create migration for `purchase_order_items` table:
    - `id`
    - `purchase_order_id` (Foreign key)
    - `product_id` (Foreign key)
    - `product_variant_id` (Foreign key, nullable)
    - `quantity` (Integer)
    - `unit_cost` (Decimal)
    - `subtotal` (Decimal)
    - Timestamps
- Define Models (`PurchaseOrder`, `PurchaseOrderItem`) with relationships.

### 2. Business Logic (Service Layer)
- Create `PurchaseOrderService` in `app/Services`.
- Implement `getAllPurchaseOrders()` with FlexSearch support.
- Implement `storePurchaseOrder(array $data)`:
    - Generate unique PO number.
    - Calculate totals.
    - Handle item insertions.
    - Trigger email if `notify_supplier` is checked.
- Implement `updatePurchaseOrder(PurchaseOrder $po, array $data)`.
- Implement `updateStatus(PurchaseOrder $po, string $status)`.
- Implement `deletePurchaseOrder(PurchaseOrder $po)`.

### 3. Validation (Form Requests)
- Create `PurchaseOrderRequest` for validation of PO creation and updates.

### 4. Controller Layer
- Create `PurchaseOrderController` in `app/Http/Controllers/Admin`.
- Adhere to the thin controller pattern.

### 5. Frontend (Admin Panel)
- Add "Purchase Orders" to the Inventory menu in the sidebar.
- Create Index view with FlexSearch and AJAX sorting.
- Create Create/Edit form:
    - Supplier selection.
    - Dynamic product/variant rows (using jQuery).
    - "Notify by Mail" checkbox.
- Create Show view for PO details.
- Create a printable PO view (optional but helpful).

### 6. Email Functionality
- Create `PurchaseOrderMail` mailable.
- Implement a professional template for the PO details sent to the supplier.

### 7. Finalization
- Run `./vendor/bin/pint --dirty`.
- Run `php artisan optimize`.
- Update `PROJECT_DOCUMENTATION.md`.

## 3. Verification Criteria
- [ ] Admin can create a PO with multiple products and variants.
- [ ] PO number is uniquely generated.
- [ ] Supplier receives an email if the notification checkbox is checked.
- [ ] Status can be transitioned between Draft, Sent, and Delivered.
- [ ] Index page supports live search/filter/sort via FlexSearch.
- [ ] Documentation is updated with the new module details.
