# smart-ecom Project Documentation

## 1. Project Overview
**smart-ecom** is a high-performance, modern e-commerce platform built with **Laravel 12**. It uses a dual-interface architecture: a comprehensive **Admin Panel** for business operations and a sleek **Client Frontend** for customers. The system is designed for high data integrity, especially regarding inventory, financials, and order fulfillment.

---

## 2. Core Architectural Standards
- **Service Layer Pattern:** 100% of business logic resides in `app/Services`. Controllers are strictly for routing.
- **Form Requests:** All validation is handled by dedicated Request classes.
- **FlexSearch Engine:** All searching and filtering in the Admin Panel use AJAX-driven FlexSearch for speed and consistency.
- **Atomic Operations:** Inventory and financial updates are wrapped in DB Transactions to ensure zero data loss.

---

## 3. Module Breakdown

### 3.1 Inventory Management (The Core)
- **What (Business Purpose):** Tracks the physical availability and movement of products across multiple warehouses, ensuring stock accuracy and procurement traceability.
- **How it Works (Technical Flow):**
    1. **Onboarding:** Admin creates **Warehouses** and **Suppliers**.
    2. **Purchase Order (PO):** Admin creates a PO for products/variants. Upon receipt, the system generates **Batches** and **Serial Numbers** (if tracked).
    3. **Stock Updates:** Receiving a PO triggers a multi-table update: `products`, `product_variants`, `batches`, `batch_products`, and `inventory_levels`.
    4. **Stock Ledger:** Every movement (Sale, Return, PO Receipt, Adjustment) logs a granular entry in `stock_ledgers` for audit purposes.
- **Data & Storage (DB Connectivity):**
    *   `batches` connect to `suppliers` and `warehouses`.
    *   `batch_products` link products/variants to specific batches with `unit_cost`.
    *   `batch_serials` track individual physical units linked to a `batch_id` and `order_item_id` (when sold).
    *   `inventory_levels` provide the current snapshots per warehouse/batch/product.

### 3.2 Order Management & Fulfillment
- **What (Business Purpose):** Manages the lifecycle of a customer purchase from checkout to final delivery and stock deduction.
- **How it Works (Technical Flow):**
    1. **Checkout:** Guest or User places an order. `orders` and `order_items` are created. Stock is NOT deducted yet.
    2. **Shipping (Allocation):** Admin changes status to 'Shipped'. A full-width allocation interface appears. Admin **MUST** select specific warehouses, batches, and serials to fulfill the order.
    3. **Shipment Record:** Fulfillment data is stored in `ordered_product_batches`. Serial `stock_status` moves to `shipped`.
    4. **Delivery:** Status move to 'Delivered'. This triggers the **Final Stock Deduction**. `batch_serials` move to `sold`, and global stock levels in `products`/`variants` are decremented.
- **Data & Storage (DB Connectivity):**
    *   `orders` link to `users` and `shipping_methods`.
    *   `order_items` link to `orders` and `products`.
    *   `ordered_product_batches` bridge `order_items` to the `batches` table to track exactly which stock source was used.

### 3.3 Return Module (RMA)
- **What (Business Purpose):** Handles the return of delivered products, ensuring correct stock restoration and financial reconciliation.
- **How it Works (Technical Flow):**
  1. **Request:** Customers/Guests submit a return request for 'Delivered' items with proof images.
  2. **Approval (Allocation):** Admin selects specific batches/serials to return. The system **only** allows selection of units originally shipped for that order.
  3. **Condition:** Admin marks items as 'Intact' (Restock) or 'Damage' (Wastage).
  4. **Receiving:** Admin marks as 'Received'. 
     *   **Intact:** Stock is restored to batches/warehouses. Serials marked as `in_stock`. Sales totals are reduced in `orders` and `order_items`. Aggregate stock ledger entry created with `RETURN_INTACT` type.
     *   **Damage:** Serials marked as `damaged` with `stock_status = 'wastage'`. Item added to `wastages`. Sales totals reduced.
- **Data & Storage (DB Connectivity):**
  *   `returns` link to `orders.id` and `users.id`.
  *   `return_items` bridge `returns.id` to `products.id` and `batches.id`. It tracks the specific `batch_serial_id` being returned.
  *   `return_images` store multiple paths linked to `returns.id`.
  *   `wastages` store records of damaged returns for loss tracking.

### 3.4 Supplier RMA (Return to Vendor)
- **What (Business Purpose):** Allows the business to return damaged or incorrect products back to the supplier for credit or replacement.
- **How it Works (Technical Flow):**
  1. **Creation:** Admin selects a Supplier and an existing Purchase Order.
  2. **Item Selection:** Admin selects specific batches and serial numbers (marked as damaged) to return.
  3. **Workflow:** Transitions from `Pending` -> `Approved` -> `Shipped` -> `Closed`.
  4. **Stock Deduction:** Upon closing, stock is permanently removed from the system, and a `RTV_Dispatch` entry is logged in the `stock_ledgers`.
- **Data & Storage (DB Connectivity):**
  *   `supplier_rmas` link to `suppliers` and `purchase_orders`.
  *   `supplier_rma_items` track the specific quantities and serials being sent back.

### 3.5 Flash Sale & Pricing Engine
- **What (Business Purpose):** Manages time-limited promotional pricing to drive sales while maintaining margin visibility.
- **How it Works (Technical Flow):**
    1. **Configuration:** Admin sets a global Flash Sale status and duration.
    2. **Selection:** Admin adds products/variants to the sale with specific discount percentages.
    3. **Auto-Expiry:** A scheduled command (`REQ-69`) runs every minute to deactivate sales and reset product discounts once the end-date is reached.
    4. **Sorting:** The shop page uses complex SQL to sort by the *effective* price (Base Price vs. Variant Price vs. Discounted Price).
- **Data & Storage (DB Connectivity):**
    *   `flash_sales` master configuration.
    *   `flash_sale_items` link `flash_sales` to `products`.

### 3.6 RBAC (Role-Based Access Control)
- **What (Business Purpose):** Secures the Admin Panel by ensuring users only access the modules and operations permitted by their role.
- **How it Works (Technical Flow):**
    1. **Permissions:** Admin creates permissions using the `menu.operation` format (e.g., `returns.edit`).
    2. **Roles:** Admin creates Roles (e.g., Inventory Manager) and assigns a group of permissions.
    3. **Assignment:** Roles are assigned to Admin users.
    4. **Enforcement:** Middleware and `@can` directives check the user's role/permissions before rendering UI or executing routes.
- **Data & Storage (DB Connectivity):**
    *   Uses `spatie/laravel-permission` tables: `roles`, `permissions`, `model_has_roles`, `role_has_permissions`.

### 3.7 Coupon System
- **What (Business Purpose):** Provides promotional discount codes to customers during checkout to increase conversion rates.
- **How it Works (Technical Flow):**
    1. **Management:** Admin creates coupons with type (Fixed/Percent), value, and usage limits.
    2. **Application:** Customer enters code at checkout. AJAX validates the code, expiry, and usage.
    3. **Calculation:** Discounts are applied to the `subtotal`.
    4. **Tracking:** Every application is logged in `coupon_usages` for audit.
- **Data & Storage (DB Connectivity):**
    *   `coupons` store rules and constraints.
    *   `coupon_usages` link `coupons` to `users` and `orders`.

### 3.8 Sales Reporting Module
- **What (Business Purpose):** Provides a comprehensive overview of the business's financial performance, enabling data-driven decision-making through real-time sales metrics, profit analysis, and granular breakdowns.
- **How it Works (Technical Flow):**
    1. **Data Aggregation:** The `ReportService` performs complex SQL aggregations on `orders`, `order_items`, and `ordered_product_batches`.
    2. **Metrics Calculation:**
        *   **Net Sales:** Calculated from `total_amount` (after all discounts).
        *   **Profit & Margin:** Derived by subtracting `total_cost` (procurement cost captured during fulfillment) from Net Sales.
        *   **AOV:** Average Order Value based on filtered results.
    3. **Time Grouping:** Users can toggle between Daily, Weekly, Monthly, and Yearly views, with dynamic SQL `DATE_FORMAT` grouping.
    4. **Filtering:** Deep filtering by Warehouse, Brand, Category, Product, and Payment/Order statuses allows for multi-dimensional analysis.
    5. **Breakdowns:** Secondary tables provide the Top 10 rankings for Products, Warehouses, Batches, and Payment Methods to identify high-performing entities.
- **Data & Storage (DB Connectivity):**
    *   `orders` & `order_items`: Primary sources for revenue, discounts, and units sold.
    *   `ordered_product_batches`: Used to link sales to specific **Warehouses** and **Batches** for cost and location-based reporting.
    *   `products` & `categories`: Linked for brand and category-level performance analysis.

### 3.9 Inventory Reporting Module
- **What (Business Purpose):** Provides real-time and historical visibility into stock levels and financial valuation of inventory, enabling accurate balance sheet reporting and procurement planning.
- **How it Works (Technical Flow):**
    1. **Historical Logic (As-of Date):** When a date is selected, the `ReportService` queries the `stock_ledgers` table to sum all `change_qty` values for every batch up to the end of that specific date. This effectively "replays" the history to find the exact stock level at that point in time.
    2. **Current Logic:** Without a date, the system uses the live `batch_products` table for instant performance.
    3. **Valuation:** The system multiplies the quantity (saleable/damaged) by the `unit_cost` stored in the `batch_products` table for each specific batch to calculate the true procurement value of the inventory.
    4. **Filtering:** Granular filters allow grouping by Warehouse, Supplier, Batch, or Catalog segments (Product/Category/Brand).
- **Data & Storage (DB Connectivity):**
    *   `batch_products`: The primary source for `unit_cost` and current `saleable_qty`.
    *   `stock_ledgers`: The source of truth for historical "As-of date" snapshots.
    *   `warehouses` & `suppliers`: Linked via `batches` to provide location and vendor-based valuation.

---

## 4. Key Procedural Lifecycle: Stock Movement Ledger

To maintain 100% accuracy, the **Stock Ledger** is the ultimate source of truth. Every module that touches stock must follow this flow:

1. **Trigger:** An action occurs (PO Received, Order Delivered, Return Received, Adjustment).
2. **Atomic Update:** The service calculates the new stock levels.
3. **Ledger Entry:** A record is created in `stock_ledgers`:
    *   `change_qty`: The positive or negative change.
    *   `transaction_type`: e.g., `PO_RECEIPT`, `SALE`, `RETURN_INTACT`, `ADJUSTMENT`.
    *   `reference_id`: The ID of the related Order, PO, or Return.
    *   `unit_cost`: The cost at the time of movement (sourced from `batch_products`).

---
*Note: This documentation is the source of truth for the smart-ecom project and is updated as the project evolves.*
