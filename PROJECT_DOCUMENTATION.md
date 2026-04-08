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
    2. **Shipping (Allocation & Deduction):** Admin changes status to 'Shipped'. A full-width allocation interface appears. Admin **MUST** select specific warehouses, batches, and serials to fulfill the order. This action triggers the **Final Stock Deduction**. `batch_serials` move to `sold`, global stock levels in `products`/`variants` are decremented, and ledger entries are logged.
    3. **Shipment Record:** Fulfillment data is stored in `ordered_product_batches`.
    4. **Delivery:** Status move to 'Delivered'. This marks the order as complete and updates payment/sales count metrics.
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

### 3.10 Stock Reporting Module
- **What (Business Purpose):** Provides a granular view of current stock status, movement history, and batch-level insights to ensure optimal inventory health and alert administrators to low-stock or stagnant items.
- **How it Works (Technical Flow):**
    1. **Data Integration:** The `ReportService` integrates data from `inventory_levels`, `batches`, `batch_products`, `stock_ledgers`, and `warehouse_stock_limits`.
    2. **Stock Calculation Fix (REQ-128):** Every record is uniquely identified by the combination of `batch_id`, `product_id`, and `product_variant_id`. All JOINS strictly use these three keys to prevent row duplication and ensure calculation accuracy.
    3. **Low-Stock Alerts:** The system compares `current_quantity` against either `warehouse_stock_limits.min_stock` (if defined) or the global `products.min_stock_global` to flag low-stock items.
    4. **Batch Aging Logic:** Calculates the number of days since receipt (`DATEDIFF`) to categorize inventory health:
        *   **Fresh:** 0 - 30 days old.
        *   **Aging:** 31 - 90 days old.
        *   **Stagnant:** 91+ days old (flagged for immediate review/promotion).
    5. **Stock Movement Trace:** Provides a chronological audit trail of all transactions for specific products, warehouses, or batches via `stock_ledgers`.
    6. **Serial Tracing:** Allows administrators to track the lifecycle of individual physical units (serials) from receipt to sale or wastage.
- **Data & Storage (DB Connectivity):**
    *   `inventory_levels`: The primary source for current quantity snapshots.
    *   `batch_products`: Linked to provide `unit_cost` for valuation metrics.
    *   `stock_ledgers`: Source for movement history.
    *   `warehouse_stock_limits`: Source for warehouse-specific threshold overrides.
    *   `suppliers`: Linked via `batches` (using `leftJoin` to support all batch types) to identify vendors.

### 3.11 Warehouse Performance Module
- **What (Business Purpose):** Evaluates warehouse operational efficiency, quality control, and inventory health to optimize fulfillment and minimize losses.
- **How it Works (Technical Flow):**
    1. **Stock Reconciliation Logic:** Uses `stock_ledgers` to verify physical stock integrity using standardized transaction types.
        *   **Formula:** `Opening Stock` + `Total Inflows` - `Total Outflows` = `Total Physical Closing Stock`.
        *   **Inflows (Physical Additions):**
            *   **PO_RECEIPT:** Saleable units from Purchase Orders.
            *   **DAMAGED:** Units arriving damaged from the supplier (added to Damaged Pool).
            *   **STOCK_ADJUSTMENT:** Positive manual corrections (where `change_qty > 0`).
            *   **RETURN_INTACT:** Saleable returns from customers.
            *   **RETURN_DAMAGED:** Damaged returns from customers (added to Damaged Pool, then counted as Wastage).
        *   **Outflows (Physical Removals/Losses):**
            *   **SALE:** Units shipped to customers.
            *   **RTV_DISPATCH:** Units returned to the supplier.
            *   **WAREHOUSE_DAMAGE:** Units lost to internal handling issues.
            *   **STOCK_ADJUSTMENT:** Negative manual corrections (where `change_qty < 0`).
    2. **Fulfillment Efficiency Metrics:**
        *   **Gross Fill Rate:** Measures stock availability and fulfillment capability.
            *   **Formula:** `(Units Shipped / Initial Demand) * 100`.
            *   **Technical Detail:** `Units Shipped` and `Initial Demand` are both derived from the `stock_ledger` 'SALE' entries to ensure accurate warehouse-level attribution and avoid double-counting in split orders.
        *   **Net Fill Rate:** Measures true fulfillment success by accounting for customer satisfaction and product quality.
            *   **Formula:** `((Units Shipped - Total Returns) / Initial Demand) * 100`.
            *   **Logic:** `Total Returns` includes both `RETURN_INTACT` and `RETURN_DAMAGED`.
        *   **Return Rate:** Percentage of shipped units that were sent back. `(Total Returns / Units Shipped) * 100`.
    3. **Quality & Wastage Metrics:**
        *   **Total Wastage Qty:** The sum of units lost due to internal errors or customer dissatisfaction.
            *   **Formula:** `WAREHOUSE_DAMAGE` + `RETURN_DAMAGED`.
        *   **Wastage Rate:** Measures warehouse operational quality. 
            *   **Formula:** `(Total Wastage Qty / Total Inflows) * 100`.
        *   **Supplier Damaged (PO):** Specifically tracks units arriving damaged from the vendor via the `DAMAGED` ledger type. These are accounted for in inflows but excluded from the internal *Wastage Rate* to ensure fair warehouse performance evaluation.
    4. **Inventory Health & Velocity:**
        *   **Stock Turnover:** Measures warehouse efficiency by calculating how many times inventory is "cycled" or sold during the period. 
            *   **Formula:** `Cost of Goods Sold (COGS) / Current Inventory Value`.
            *   **Logic:** COGS is derived from the `subtotal_cost` in `ordered_product_batches` (actual procurement cost), while Inventory Value is the sum of `current_quantity * unit_cost` for all saleable items on hand.
        *   **Slow-Moving SKUs:** The percentage of unique SKUs that had zero sales activity during the selected period.
        *   **Low-Stock SKUs:** Real-time count of items currently below their defined minimum thresholds.
    5. **Reporting & Exporting:**
        *   **Excel Export:** Generates multi-column spreadsheets including all movement types (Received, Sold, Returns, RTV, Adjustments) and efficiency KPIs. Uses `WarehousePerformanceExport` for consistent formatting.
        *   **Print Functionality:** Implements custom `printReportCard` (summary) and `printFullReport` (detail) JS logic to provide clean, PDF-ready snapshots of warehouse performance data, excluding non-essential UI elements like filters and navigation buttons.
- **Data & Storage (DB Connectivity):**
    *   `stock_ledgers`: Source for all historical movement trends.
    *   `inventory_levels`: Source for live closing stock snapshots (`total_closing_stock`).
    *   `ordered_product_batches`: Source for warehouse-specific fulfillment attribution and COGS calculation.
    *   `return_items`: Source for calculating return-based efficiency penalties.
    *   `warehouse_stock_limits`: Source for localized alert thresholds.



---

## 4. Key Procedural Lifecycle: Stock Movement Ledger (Source of Truth)

### 3.11 Warehouse Performance Report (REQ-129, REQ-132)
**Business Purpose:** To monitor and improve warehouse operational quality by tracking efficiency, accuracy, and stock health metrics.

**Stock Reconciliation (Source of Truth):**
To maintain 100% operational accuracy, the **Stock Ledger** (`stock_ledgers` table) is the absolute source of truth for all movement-based reporting. Every physical change to inventory MUST log a ledger entry to ensure mathematically sound reports.

1. **Trigger:** An action occurs (PO Received, Order Delivered, Return Received, Adjustment, Damage Discovery).
2. **Atomic Update:** The service updates the `inventory_levels` and `batch_products` tables.
3. **Ledger Entry:** A record is created in `stock_ledgers` using standardized naming:
    *   **Inflow Types:**
        *   `PO_RECEIPT`: Saleable stock added from Purchase Orders.
        *   `DAMAGED`: Stock arriving damaged from suppliers (Added to Damaged Pool).
        *   `RETURN_INTACT`: Saleable units returned by customers.
        *   `RETURN_DAMAGED`: Damaged units returned by customers (Added to Damaged Pool, then counted as Wastage).
        *   `STOCK_ADJUSTMENT`: Positive manual corrections (`change_qty > 0`).
    *   **Outflow Types:**
        *   `SALE`: Units shipped and delivered to customers.
        *   `RTV_DISPATCH`: Units returned to vendors (RTV).
        *   `WAREHOUSE_DAMAGE`: Units lost due to internal handling (Wastage).
        *   `STOCK_ADJUSTMENT`: Negative manual corrections (`change_qty < 0`).

**Key Performance Indicators (KPIs):**
1. **Fulfillment Efficiency Metrics:**
    *   **Gross Fill Rate:** Measures stock availability and fulfillment capability.
        *   **Formula:** `(Units Shipped / Initial Demand) * 100`.
        *   **Technical Detail:** `Units Shipped` and `Initial Demand` are both derived from the `stock_ledger` 'SALE' entries to ensure accurate warehouse-level attribution and avoid double-counting in split orders.
    *   **Net Fill Rate:** Measures true fulfillment success by accounting for customer satisfaction and product quality.
        *   **Formula:** `((Units Shipped - Total Returns) / Initial Demand) * 100`.
        *   **Logic:** `Total Returns` includes both `RETURN_INTACT` and `RETURN_DAMAGED`.
    *   **Return Rate:** Percentage of shipped units that were sent back. `(Total Returns / Units Shipped) * 100`.

2. **Quality & Wastage Metrics:**
    *   **Total Wastage Qty:** The sum of units lost due to internal errors or customer dissatisfaction.
        *   **Formula:** `WAREHOUSE_DAMAGE` + `RETURN_DAMAGED`.
    *   **Wastage Rate:** Measures warehouse operational quality. 
        *   **Formula:** `(Total Wastage Qty / Total Inflows) * 100`.
    *   **Supplier Damaged (PO):** Specifically tracks units arriving damaged from the vendor via the `DAMAGED` ledger type. These are accounted for in inflows but excluded from the internal *Wastage Rate* to ensure fair warehouse performance evaluation.

3. **Inventory Health & Velocity:**
    *   **Stock Turnover:** Measures warehouse efficiency by calculating how many times inventory is "cycled" or sold during the period. 
        *   **Formula:** `Cost of Goods Sold (COGS) / Current Inventory Value`.
        *   **Logic:** COGS is derived from the `subtotal_cost` in `ordered_product_batches` (actual procurement cost), while Inventory Value is the sum of `current_quantity * unit_cost` for all saleable items on hand.
    *   **Slow-Moving SKUs:** The percentage of unique SKUs that had zero sales activity during the selected period.
    *   **Low-Stock SKUs:** Real-time count of items currently below their defined minimum thresholds.

4. **Reporting & Exporting:**
    *   **Excel Export:** Generates multi-column spreadsheets including all movement types (Received, Sold, Returns, RTV, Adjustments) and efficiency KPIs. Uses `WarehousePerformanceExport` for consistent formatting.
    *   **Printable View:** Securely prints the performance table with consistent headers and branding using client-side JavaScript.

### 3.12 Low Stock Notifications & Automation (REQ-133)
**Business Purpose:** To proactively manage inventory levels and prevent stockouts by automating the identification and communication of low-stock items.

**How it Works:**
*   **Threshold Monitoring:** The system continuously monitors stock levels at both the **Global** (product-wide) and **Warehouse** (location-specific) levels.
*   **Notification Settings:** A "Notification Email" field in the *General Settings* module defines the recipient for automated stock alerts.
*   **Automated Scheduling:** A daily background task scans for items that have reached or dropped below their minimum thresholds. 
    *   **Artisan Command:** `php artisan inventory:check-low-stock`
    *   **Schedule:** Runs daily at 09:00 (configured in `routes/console.php`).
*   **Email Reports:** For all flagged items, the system generates an email report that includes:
    *   **Product/Variant Details:** Name, variant, and SKU.
    *   **Current Inventory:** Live stock levels at the specific warehouse or global level.
    *   **Suggested Restock:** An intelligent suggestion for reordering to restore inventory to a safe buffer level.
        *   **Formula:** `(Minimum Threshold * 2) - Current Quantity`.
        *   **Replenishment Floor:** A hard floor of 10 units is enforced to ensure restock suggestions are procurement-viable for items with very low thresholds.
*   **Anti-Spam Logic:** To prevent alert fatigue, the system tracks the `last_alert_sent` timestamp on individual inventory levels. It only sends a new notification for the same item if at least 24 hours have passed since the last alert.
*   **Manual Trigger:** Admins can manually initiate a scan and notification sequence directly from the *Dashboard* using the "Check & Notify Now" action.
    *   **Route:** `admin.inventory.check-low-stock` (URL: `/admin/inventory/check-low-stock`).
    *   **Usage:** Can be used as a webhook for external cron services if required.

### 3.13 Bulk Product Upload (REQ-51, REQ-134)
**Business Purpose:** To enable rapid, high-volume catalog onboarding and updates through Excel/CSV imports, ensuring consistency with the system's architectural standards.

**How it Works:**
*   **Template Generation:** Admins can download standardized CSV/XLSX templates via `ProductTemplateExport`. These templates define the exact column structure required for a successful import.
*   **Idempotent Import:** The `ProductsImport` logic uses `updateOrCreate` based on the **Product Name**, allowing for both the creation of new items and the updating of existing ones without duplication.
*   **Variant Handling:** Supports multi-row product definitions where variant details (Size, Color, SKU) are linked to the parent product identified in the same or preceding rows.
*   **Service Alignment:** The import process is strictly aligned with the `ProductService::storeProduct` logic, ensuring that all marketing flags (`is_featured`, etc.) and minimum stock thresholds are correctly populated.
*   **Ledger Consistency:** In line with the system's inventory architecture, bulk-uploaded products are initialized with `0` stock. Physical inventory must subsequently be added via the *Purchase Order* or *Stock Adjustment* modules to maintain ledger integrity.

### 3.14 Admin UI Standardization
- **What (Business Purpose):** Ensures a clean, consistent, and professional appearance across all administrative data tables by removing redundant UI elements.
- **How it Works (Technical Flow):**
    1. **Pagination Refactoring:** Manual "Showing X to Y of Z Results" text blocks were removed from all admin table partials.
    2. **Laravel Integration:** The system now relies exclusively on Laravel's native Bootstrap 5 pagination `links()` method.
    3. **Layout Consistency:** This change leverages the built-in Bootstrap 5 pagination template, which automatically includes the "Showing..." text and provides a responsive `d-flex` layout.
    4. **Simplification:** The `card-footer` in all table partials is simplified to a single line: `{{ $data->links() }}`, reducing code duplication and maintenance overhead.
- **Data & Storage (Files Updated):**
    *   Updated 15+ partial files in `resources/views/admin/*/partials/table.blade.php`.

## 4. Technical Architecture

### 4.1 Database Seeding & Data Integrity (REQ-134)
**Business Purpose:** To provide a reliable way to populate the system with consistent, mathematically sound test data for development and demonstration.

**Key Seeder Standards:**
*   **Idempotency:** All seeders (Brand, Category, User, Admin, etc.) use `updateOrCreate` or `firstOrCreate` logic. This allows the command `php artisan db:seed` to be run multiple times without causing duplicate entry errors or unique constraint violations.
*   **Product Service Alignment:** The `ProductSeeder` is strictly aligned with the `ProductService::storeProduct` method. It creates products and variants with marketing flags and minimum thresholds while maintaining ledger-based inventory logic (initial stock is always 0).
*   **Hierarchical Integrity:** The `CategorySeeder` correctly maps parent-child relationships using slugs, ensuring the catalog hierarchy is properly preserved.

---
*Note: This documentation is the source of truth for the smart-ecom project and is updated as the project evolves.*
