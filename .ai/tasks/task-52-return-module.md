# Task 52: Return Module Implementation

Implement a comprehensive return management system for both guests and authenticated users, including admin approval workflow, stock management, and wastage tracking.

## 1. Database Schema

### `returns` Table
- `id` (Primary Key)
- `order_id` (Foreign Key to `orders`)
- `return_id` (Unique string, e.g., RET-123456)
- `user_id` (Nullable Foreign Key to `users`)
- `reason` (Text)
- `status` (Enum: pending, approved, rejected, received)
- `rejection_reason` (Text, Nullable)
- `image` (String, Nullable)
- `created_at`, `updated_at`

### `return_items` Table
- `id` (Primary Key)
- `return_id` (Foreign Key to `returns`)
- `product_id` (Foreign Key to `products`)
- `product_variant_id` (Nullable Foreign Key to `product_variants`)
- `quantity` (Integer)
- `unit_price` (Decimal)
- `total_price` (Decimal)
- `condition` (Enum: pending, damage, intact) - Set during admin approval
- `is_received` (Boolean, default: false)
- `created_at`, `updated_at`

### `wastages` Table
- `id` (Primary Key)
- `product_id` (Foreign Key to `products`)
- `product_variant_id` (Nullable Foreign Key to `product_variants`)
- `quantity` (Integer)
- `reason` (String)
- `return_id` (Nullable Foreign Key to `returns`)
- `created_at`, `updated_at`

## 2. Implementation Steps

### Step 1: Backend Setup (Migrations & Models)
- [x] Create migrations for `returns`, `return_items`, and `wastages`.
- [x] Create models: `ReturnRequest`, `ReturnItem`, `Wastage`.
- [x] Define relationships in `Order`, `Product`, and `ProductVariant`.

### Step 2: Service Layer & Form Requests
- [x] Create `ReturnService` to handle all logic.
- [x] Create `ReturnRequestStoreRequest` for client-side submission.
- [x] Create `ReturnRequestStatusUpdateRequest` for admin-side actions.

### Step 3: Client-Side Implementation
- [x] Create a "Return Request" page for guests (Route: `/returns`).
- [x] Implement AJAX to fetch order details by Order ID.
- [x] **Restricted Return Access:** Only allow fetching product details for returns if the order status is 'Delivered'.
- [x] Build the return form: select items, quantity, upload photo, reason.
- [x] Add "Return" button in authenticated user's order details page.
- [x] Implement return status tracking by Order ID.

### Step 4: Admin-Side Implementation
- [x] Create "Return Section" in Admin Sidebar.
- [x] **Submenu: Return Requests**
    - [x] List all requests with FlexSearch (filters: status, date).
    - [x] View request details.
    - [x] Approve/Reject logic (Approve -> Select Condition; Reject -> Add Reason).
    - [x] Change Receive Status from "Processing" to "Received".
- [x] **Submenu: Returned Products**
    - [x] List all items with `is_received = true`.
- [x] **Submenu: Wastage/Damaged Products**
    - [x] List all items in `wastages` table.

### Step 5: Logic Integration (Receiving Workflow)
- [x] When status changes to "Received":
    - [x] If Condition is "Intact":
        - [x] Increment stock (Base or Variant).
        - [x] Decrease total sales and product sales value.
    - [x] If Condition is "Damage":
        - [x] Add record to `wastages` table.

### Step 6: Finalization
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Verify with existing Seeders and manual testing.
- [x] Run `php artisan optimize`.

## 3. Verification Criteria
- [x] Guest can submit return request with valid Order ID.
- [x] Authenticated user can initiate return from order history.
- [x] Admin can approve/reject requests with mandatory condition/reason.
- [x] "Received" status triggers stock restoration for intact items.
- [x] "Received" status triggers wastage entry for damaged items.
- [x] Sales data is correctly adjusted upon receiving intact returns.
- [x] FlexSearch works on all return-related index pages.
- [x] Documentation updated in `PROJECT_DOCUMENTATION.md`.
