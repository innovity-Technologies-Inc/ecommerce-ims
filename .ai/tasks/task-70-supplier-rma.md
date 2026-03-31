# Task 70: Supplier RMA (Return to Vendor) Module

Implement a module for admins to return damaged products to vendors, including PO/Batch selection, serial tracking, and status workflow.

## 1. Database Schema

### `supplier_rmas` Table
- `id` (Primary Key)
- `rma_number` (Unique string, e.g., SRMA-20260330-001)
- `supplier_id` (Foreign Key to `suppliers`)
- `purchase_order_id` (Nullable Foreign Key to `purchase_orders`)
- `status` (Enum: pending, approved, shipped, closed)
- `notify_supplier` (Boolean, default: false)
- `remarks` (Text, Nullable)
- `created_at`, `updated_at`

### `rma_items` Table
- `id` (Primary Key)
- `supplier_rma_id` (Foreign Key to `supplier_rmas`)
- `batch_id` (Foreign Key to `batches`)
- `product_id` (Foreign Key to `products`)
- `product_variant_id` (Nullable Foreign Key to `product_variants`)
- `quantity` (Integer)
- `created_at`, `updated_at`

### `batch_serials` Modification
- Update `stock_status` enum to include `returned`.

## 2. Implementation Steps

### Step 1: Backend Setup (Migrations & Models)
- [x] Create migration for `supplier_rmas` and `rma_items`.
- [x] Create migration to update `batch_serials` enum.
- [x] Create models: `SupplierRma`, `RmaItem`.
- [x] Define relationships in `Supplier`, `PurchaseOrder`, `Batch`, `Product`, `ProductVariant`.

### Step 2: Service Layer & Form Requests
- [x] Create `SupplierRmaService` to handle all logic.
- [x] Create `SupplierRmaStoreRequest` for validation.
- [x] Create `SupplierRmaStatusUpdateRequest` for status transitions.

### Step 3: Admin Controller & UI
- [x] Create `SupplierRmaController`.
- [x] **Index Page:**
    - [x] List all RMAs with FlexSearch (filters: status, supplier, date).
- [x] **Create Page:**
    - [x] Select Supplier or PO (Dynamic filtering).
    - [x] Dynamically load Batches for damaged products.
    - [x] Modal-based serial number selection (Checkbox).
    - [x] Quantity logic: Default to all damaged or lock to selected serial count.
    - [x] "Notify Email" toggle switch.
- [x] **Show/Edit Page:**
    - [x] View RMA details and items.
    - [x] Update status workflow.

### Step 4: Logic Integration (Status Workflow)
- [x] **On Creation:**
    - [x] Set status to `pending`.
    - [x] If `notify_supplier` is true, trigger email (Mailable).
- [x] **On Status Change:**
    - [x] Validate transition (Pending -> Approved -> Shipped -> Closed).
- [x] **On `Closed` Status:**
    - [x] Log `StockLedger` entry (`transaction_type = RTV_Dispatch`, `section_name = Supplier RMA`).
    - [x] Update `batches` table: `total_damaged_qty = 0`.
    - [x] Update `batch_products` table: `damaged_qty = 0`.
    - [x] Update `batch_serials` table: `product_status = damaged_return`, `stock_status = returned` for the selected serials.

### Step 5: Finalization
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Verify with existing Seeders and manual testing.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## 3. Verification Criteria
- [x] Admin can create RMA by selecting Supplier/PO and Damaged Batches.
- [x] Serial numbers are correctly tracked and selected.
- [x] Email notification is triggered if toggled.
- [x] Status workflow enforces correct transitions.
- [x] Closing RMA correctly updates stock, batches, serials, and logs to ledger.
- [x] FlexSearch works on RMA index.
- [x] Documentation updated in `PROJECT_DOCUMENTATION.md`.
