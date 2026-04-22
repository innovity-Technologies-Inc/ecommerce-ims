# smart-ecom Project Documentation

## 1. Project Overview
**smart-ecom** is a high-performance, modern e-commerce platform built with **Laravel 12**. It uses a dual-interface architecture: a comprehensive **Admin Panel** for business operations and a sleek **Client Frontend** for customers. The system is designed for high data integrity, especially regarding inventory, financials, and order fulfillment.

---

### **2. Core Architectural Standards**
- **Service Layer Pattern:** 100% of business logic resides in `app/Services`. Controllers are strictly for routing.
- **Form Requests:** All validation is handled by dedicated Request classes.
- **Admin Activity Tracking (REQ-222):** All primary models use the `TracksAdminActivity` trait. This automatically populates `created_by` and `updated_by` fields (foreign keys to `admins`) during creation and update operations performed by an authenticated admin.
- **FlexSearch Engine (v4.0.0+):** All searching and filtering in the Admin Panel use AJAX-driven FlexSearch for speed and consistency. It supports multi-column filtering, relationship searching, and dynamic sorting.
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
    1. **Checkout:** Guest or User places an order. `orders` and `order_items` are created. 
       *   **Cost Logic:** At this stage, `total_cost` is **0.00**. This is because the system does not yet know which specific physical batch (and its associated unit cost) will be used to fulfill the order.
    2. **Processing:** Admin moves the order to 'Processing' while picking and packing.
       *   **Transitions:** From 'Processing', an order can be transitioned to either **'Shipped'** (to begin fulfillment) or **'Cancelled'** (if the order cannot be fulfilled).
    3. **Shipping (Allocation & Deduction):** Admin changes status to 'Shipped'. A full-width allocation interface appears. Admin **MUST** select specific warehouses, batches, and serials to fulfill the order. 
       *   **Final Cost Capture:** This action triggers the **Final Stock Deduction** and **Cost Capture**. The system fetches the `unit_cost` from the selected batches and updates the `total_cost` in the `orders` and `order_items` tables.
       *   **Ledger:** `batch_serials` move to `sold`, global stock levels in `products`/`variants` are decremented, and ledger entries are logged.
    4. **Shipment Record:** Fulfillment data is stored in `ordered_product_batches`.
    5. **Delivery:** Status move to 'Delivered'. This marks the order as complete and updates payment/sales count metrics.
- **Data & Storage (DB Connectivity):**
    *   `orders` link to `users` and `shipping_methods`.
    *   `order_items` link to `orders` and `products`.
    *   `ordered_product_batches` bridge `order_items` to the `batches` table to track exactly which stock source was used.

### 3.3 Return Module (RMA)
- **What (Business Purpose):** Handles the return of delivered products, ensuring correct stock restoration and financial reconciliation through a multi-step verification process.
- **How it Works (Technical Flow):**
  1. **Request:** Customers/Guests submit a return request for 'Delivered' items with proof images.
     *   **Automation:** The system automatically sends a **Return Request Confirmation Email** to the customer upon submission (REQ-147).
  2. **Approval:** Admin reviews the request and images. They can either 'Approve' or 'Reject' (with a mandatory reason).
     *   **Automation:** A **Return Status Update Email** is sent to the customer notifying them of the admin's decision (REQ-147).
  3. **Physical Receiving & Allocation (REQ-147):** Once the physical items are received, the admin performs the allocation and condition inspection.
     *   **Granular Allocation:** Admin selects the specific batches and serial numbers to be returned (limited to those originally shipped for the order).
     *   **Condition Check:** Admin marks each item as 'Intact' (Restock) or 'Damage' (Wastage).
  4. **Processing:** Admin marks the return as 'Received'. 
     *   **Intact:** Stock is restored to batches/warehouses. Serials marked as `in_stock`. Sales totals and costs are reduced in `orders`, `order_items`, and `ordered_product_batches`. Aggregate stock ledger entry created with `RETURN_INTACT` type.
     *   **Damage:** Serials marked as `damaged` with `stock_status = 'wastage'`. Item added to `wastages`. Sales/Cost totals reduced.
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
- **What (Business Purpose):** Manages dynamic pricing, promotional discounts, and flash sales to drive customer engagement while ensuring accurate financial calculations from cart to checkout.
- **How it Works (Technical Flow):**
    1. **Dynamic Pricing Hierarchy (REQ-152):** The system follows a strict priority for calculating the *effective* selling price:
        *   **Flash Sale:** Priority 1 (Variant Flash Price > Product Flash Price).
        *   **Standard Discount:** Priority 2 (Variant Discount Price > Product Discount Price).
        *   **Regular Price:** Base Price (Variant Regular Price > Product Regular Price).
    2. **Robust Fallbacks:** If a variant-specific price or discount is missing (set to 0 or NULL), the engine automatically falls back to the parent Product's global settings. This ensures that "global discounts" are correctly applied even when products have variants.
    3. **UI Transparency:** In the cart and checkout summary, the system displays the original Regular Price (with a line-through) alongside the effective Selling Price whenever a discount is active, providing clear value visibility to the customer.
    4. **Auto-Expiry:** A scheduled command runs every minute to deactivate flash sales once the end-date is reached.
    5. **Sorting:** The shop page uses optimized SQL to sort products by their *effective* price across all variants.
- **Data & Storage (DB Connectivity):**
    *   `products` & `product_variants`: Store regular and discounted price points.
    *   `flash_sales` & `flash_sale_items`: Master configuration for time-limited promotions.

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
    7. **Reporting & Exporting (REQ-140, REQ-141, REQ-142):** 
        *   **Full-Data Excel Export (REQ-141, REQ-143):** Generates multi-column spreadsheets for all detailed views (Warehouse, Product, Batch, Movement, Aging, Wastage, Serial). Backend logic is explicitly configured to bypass UI-driven pagination parameters (by passing `null` for `perPage` limits), ensuring the exported file contains the *full* dataset matching the active filters regardless of which page the user is viewing.
        *   **Excel Format Style (Normalization):** To prevent blank cells in exported files, all numeric data is forced to string representation using `number_format($val, $decimals, '.', '')` before export. Additionally, all null, empty, or whitespace-only values are automatically normalized to a literal string `'0'` within the exported file to ensure 100% data visibility in Excel (REQ-143).
        *   **High-Fidelity Printing:** Implements a specialized `printFullReport` JS logic that captures all records (bypassing pagination) and renders a clean, professional PDF-ready snapshot with business branding, report headers, and consistent table formatting.
        *   **Detailed View Filter Preservation (REQ-142):** Filter forms in Sales, Inventory, and Stock reports automatically preserve the current detailed `view` state (e.g., "Batch Aging" or "Movement History") using hidden request parameters. This ensures that applying new date or entity filters maintains the user's active report context instead of redirecting them to the main dashboard.
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

    ### 3.12 Comprehensive Demo Seeding (REQ-178)
    - **What (Business Purpose):** Provides a robust, mathematically consistent set of fashion-related data to demonstrate the system's full capabilities across procurement, sales, and inventory management.
    - **How it Works (Technical Flow):**
        1. **Data Population:** The `ProductSeeder` generates 100 unique fashion products across categories like T-Shirts, Jeans, Shoes, and Accessories.
        2. **Image Automation:** For each product, the seeder automatically downloads 3 relevant fashion images from `loremflickr.com` and uploads them using the standard `ProductService` logic.
        3. **Inventory Traceability:** Every product is initialized with a full history:
            *   **Purchase Order:** A 'Sent' PO is created for every product/variant.
            *   **Receipt:** The PO is "received" via `PurchaseOrderService`, generating valid **Batches**, **Inventory Levels**, and **Stock Ledger** entries.
            *   **Adjustments:** Random products receive additional manual stock adjustments to demonstrate history tracking.
        4. **Idempotency:** The seeder truncates all product, stock, and ledger tables before execution, ensuring a clean and consistent starting point for demos.
    - **Data & Storage:**
        *   **Warehouses:** 20 USA state-based hubs.
        *   **Suppliers:** 15 fashion-specialized vendors.
        *   **Volume:** ~100 Products, ~250 Variants, ~300 Images, and ~120 Inventory Batches.

    ### 3.13 Scoped Unique Categories (REQ-180, REQ-181)
    - **What (Business Purpose):** Allows for identical category names (e.g., "Shoes") to exist across different parent departments (e.g., "Men" vs. "Women") while maintaining URL unique identifiers (slugs).
    - **How it Works (Technical Flow):**
        1. **Validation:** The `CategoryRequest` uses a scoped `unique` rule constrained by `parent_id`. This ensures a name is only unique within its immediate parent.
        2. **Unique Slug Engine:** The `CategoryService` implements a fallback engine for slug generation:
            *   **Standard:** `slug($name)` (e.g., `shoes`).
            *   **Conflict Fallback:** `slug($parent_name . '-' . $name)` (e.g., `men-shoes`).
            *   **Hard Conflict Fallback:** `slug($name . '-' . $id_suffix)` (e.g., `shoes-1`).
        3. **Database Integrity:** This multi-layered approach ensures the `slug` column remains globally unique in the database while fulfilling business requirements for flexible naming.

    ### 3.14 Flash Sale Discount Dynamics (REQ-188)
    - **What (Business Purpose):** Ensures that when a Flash Sale is active, the system prioritizes flash pricing and displays the correct discount badges to maximize conversion.
    - **How it Works (Technical Flow):**
        1. **Activation:** When a Flash Sale is enabled in the Admin Panel, the `FlashSaleService` updates all linked products, setting `is_flash_sale` to true and calculating `flash_discount_price` and `flash_discount_percentage`.
        2. **Priority Pricing:** The `HelperClass::getProductPriceRange` logic detects the `is_flash_sale` flag and overrides standard regular/discount pricing with Flash Sale values.
        3. **Badge Logic:** The product card template checks the calculated `has_discount` flag (derived from flash values during a sale) to render the red "-X%" discount badge.
    - **Data & Storage:**
        - **Flag:** `products.is_flash_sale` (Boolean).
        - **Pricing:** `products.flash_discount_price` and `products.flash_discount_percentage`.

    ### 3.15 PO Currency Refinement (REQ-195)
    - **What (Business Purpose):** Ensures that the Purchase Order module reflects the business's global currency setting, providing a consistent financial interface for procurement.
    - **How it Works (Technical Flow):**
        1. **Global Setting:** The system retrieves the currency symbol (e.g., $, BDT) from the **General Settings** module via `HelperClass::generalSettings()`.
        2. **Dynamic Rendering:** All Purchase Order views (List, Create, Edit, Details) and outgoing supplier emails pull the currency symbol dynamically from this global setting.
        3. **Standardization:** Replaces handcoded currency fallbacks with the user-defined setting to ensure 100% alignment with the business's operational currency.
    - **Data & Storage:**
        - **Source:** `general_settings.currency` field.

    ### 3.16 Performance & UX Optimizations (REQ-196)
    - **What (Business Purpose):** Improves the stability and responsiveness of data-heavy reports to provide a professional user experience.
    - **How it Works (Technical Flow):**
        1. **Background Optimization:** Moved complex CSS radial gradients to pseudo-elements with `pointer-events: none` to reduce GPU composite overhead during scrolling.
        2. **Hardware Acceleration:** Injected `will-change: transform` hints on high-scroll areas to trigger browser-level rendering optimizations.
        3. **AJAX UX Refinement:** Replaced aggressive container opacity changes with localized loading spinners and layout-preserving `min-height` logic in the Stock Index report to prevent content "flickering" or jumping during updates.

    ### 3.17 Modern Image Format Support (REQ-192)
        - **What (Business Purpose):** Enables the use of high-compression, modern image formats like AVIF to improve website performance and SEO.
        - **How it Works (Technical Flow):**
        1. **Validation:** All image-bearing Form Requests (Products, Categories, Brands, Sliders, etc.) have been updated to include the `avif` mime type in their validation rules.
        2. **Storage:** The `HelperClass::file_upload` logic dynamically detects the `.avif` extension and stores it in the designated public disk folders.
        3. **Rendering:** Standard `<img>` tags on the frontend render AVIF files natively on all modern browsers.
        - **Scope:** Applied across all administrative upload portals.

        ---



    ## 4. Key Procedural Lifecycle: Stock Movement Ledger (Source of Truth)

    ### 4.1 Warehouse Performance Report (REQ-129, REQ-132)

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

### 3.15 Dashboard Revenue Analytics (REQ-136)
**Business Purpose:** To provide administrators with real-time financial visibility and performance tracking directly from the dashboard.

**How it Works:**
*   **Metric Calculation:** The system calculates key financial indicators by aggregating data from the `orders` table (for Revenue/Cost/Profit) and `purchase_orders` (for Procurement volume).
    *   **Revenue:** Sum of `total_amount` (Gross Sale) for 'Delivered' orders.
    *   **Cost:** Sum of `total_cost` (Procurement Cost) for 'Delivered' orders.
    *   **Profit:** `Revenue - Cost`.
*   **Time-Series Tracking:** Metrics are grouped into Daily, Monthly, and Yearly periods for deep historical analysis.
*   **Analytical Visualization:**
    *   **Revenue vs. Cost Charts:** Multi-series charts (Area for Monthly, Bar for Yearly) comparing **Revenue, Cost, and Profit** side-by-side to track margin health.
    *   **Profit Charts:** Dedicated Monthly (Area) and Yearly (Bar) charts to focus purely on net earnings.
    *   **Orders Tracking:** Line and Bar charts showing the volume of customer orders (excluding cancelled/rejected).
    *   **Purchases Tracking:** Line and Bar charts showing the frequency and volume of supplier purchase orders.
*   **Architecture:**
    *   **Service Layer:** `DashboardService` handles complex SQL aggregations for both sales (`orders`) and procurement (`purchase_orders`).
    *   **Thin Controller:** `DashboardController` prepares data for ApexCharts visualization.
    *   **Optimized UI:** A streamlined 3-column summary layout focused on high-level Revenue and Profit KPIs.

### 3.16 Aesthetic Admin Theme Synchronization (REQ-137)
**Business Purpose:** To provide a premium, cohesive, and modern dark-emerald aesthetic across the entire administrative interface, ensuring consistency from login to the internal dashboard.

**How it Works:**
*   **Aesthetic Palette:** Transitioned from standard dark-blue to a sophisticated **Emerald Dark Theme** (`#040d0a` background with `#10b981` emerald accents).
*   **Sidebar Refinement:**
    *   **Background:** Deep emerald-black with subtle right-side glow borders.
    *   **Navigation:** Items feature smooth translateX hover effects, glowing active indicators, and high-contrast readable typography.
    *   **Hierarchy:** Menu titles are capitalized with increased letter spacing and subtle emerald tinting.
*   **Topbar Enhancement:**
    *   Integrated backdrop-blur effects and emerald-tinted button interactions.
    *   Badges and notification icons use the primary emerald glow.
*   **Global Components:**
    *   Cards, tables, and buttons are synchronized with the emerald palette.
    *   Primary action buttons use high-intensity linear gradients (Emerald to Forest Green).
*   **Architecture:** Styles are injected via the `admin.structure.master` layout using high-specificity CSS overrides to ensure a seamless transition between pages without breaking standard Bootstrap 5 functionality.

### 3.17 Homepage Section Product Selector UI (REQ-138)
**Business Purpose:** To provide a highly efficient, user-friendly, and searchable interface for manually selecting products for homepage sections, replacing the limited standard dropdown with a paginated AJAX-driven selector.

**How it Works:**
*   **Dual-Column Interface:** When "Custom" mode is selected for a homepage section (e.g., Bestsellers, Top Picks), the UI splits into two main functional areas:
    *   **Selected Products (Left):** A real-time table showing all products currently assigned to the section, including their images and prices, with instant removal capability.
    *   **Product Selector (Right):** A comprehensive search and filter panel allowing admins to find products by name, SKU, Category, Sub-Category, and Brand.
*   **AJAX-Driven Searching:** Results are loaded dynamically without page refreshes, supporting deep filtering and pagination for large product catalogs.
*   **Instant Interaction:** Adding a product from the selector instantly updates the selection table and increments the total product count badge.
*   **Unified UX:** This implementation standardizes the product selection experience across the entire admin panel, matching the advanced UI used in the Flash Sale module.
*   **Architecture:**
    *   **Controller:** `HomepageSectionController` now includes a `searchProducts` method to serve AJAX requests.
    *   **Service Integration:** Reuses `FlashSaleService` logic for consistent filtering and `ProductService` for dropdown data.
    *   **View Layer:** Uses a shared partial `admin.sections.partials.product_list` for consistent rendering of search results.

## 4. Technical Architecture

### 4.1 Database Seeding & Data Integrity (REQ-134)
**Business Purpose:** To provide a reliable way to populate the system with consistent, mathematically sound test data for development and demonstration.

**Key Seeder Standards:**
*   **Idempotency:** All seeders (Brand, Category, User, Admin, etc.) use `updateOrCreate` or `firstOrCreate` logic. This allows the command `php artisan db:seed` to be run multiple times without causing duplicate entry errors or unique constraint violations.
*   **Product Service Alignment:** The `ProductSeeder` is strictly aligned with the `ProductService::storeProduct` method. It creates products and variants with marketing flags and minimum thresholds while maintaining ledger-based inventory logic (initial stock is always 0).
*   **Hierarchical Integrity:** The `CategorySeeder` correctly maps parent-child relationships using slugs, ensuring the catalog hierarchy is properly preserved.

### 4.2 Input Normalization & Validation
**Business Purpose:** To ensure that user-provided data is correctly interpreted and stored, preventing validation failures caused by formatting inconsistencies (e.g., leading zeros in numeric fields).

**Implementation Details:**
*   **Integer Casting:** Crucial integer fields (Quantities, Limits, Percentages, Stock Thresholds) in Form Requests implement normalization logic via the `prepareForValidation()` method.
*   **Leading Zero Handling:** Inputs like "04" are explicitly cast to integers (e.g., `(int) $value`) before validation rules are applied. This ensures compatibility with the `integer` validation rule while maintaining numerical accuracy.
*   **System-Wide Coverage:** This pattern is enforced across:
    *   **Procurement:** PO Receiving, Damage Entry, Stock Adjustments.
    *   **Catalog:** Product/Variant Creation (Discount %, Min Stock).
    *   **Marketing:** Coupon Usage Limits, Homepage Section Limits.
    *   **Fulfillment:** Order Status Updates and Return Allocations.
    *   **Identity:** Custom Banners for Login and Registration pages (REQ-165).

### 4.3 Admin Notification System (REQ-163)
- **What (Business Purpose):** Provides real-time alerts to administrators for critical events requiring attention, ensuring faster response times for orders, returns, and inventory issues.
- **How it Works (Technical Flow):**
    1. **Triggers:** Specific events in the system trigger a notification:
        *   **Orders:** New order placement.
        *   **Returns:** New customer return request submission.
        *   **Messages:** New contact message received.
        *   **Inventory:** Low stock detection (daily check).
    2. **Storage:** Notifications are stored in `admin_notifications` with `is_read = false`.
    3. **Visibility:** A dynamic bell icon in the navbar shows the unread count and a preview of the latest 10 items via a **View Composer**.
    4. **Real-Time Experience:** The topbar dropdown and unread count are refreshed every **60 seconds** using background AJAX polling. The polling intelligently pauses when the browser tab is inactive (`document.hidden`) to optimize server resources.
    5. **Management:** Admins can "Mark All as Read" or click individual items to be redirected to the relevant resource (e.g., clicking an order notification takes you to the Order Details page).
    6. **History:** A dedicated index page allows filtering by **Type**, **Date**, and **Search** using **FlexSearch** and **AJAX** partial updates.
- **Data & Storage (DB Connectivity):**
    *   `admin_notifications` table stores title, message, type, url, and read status.

### 4.4 Client Auth Pages Redesign (REQ-164)
- **What (Business Purpose):** Redesigns the client-side login, registration, and password recovery pages to provide a visually appealing, modern, and cohesive identity for users.
- **How it Works (Technical Flow):**
    1. **Layout & Styling:** Implements a modern split-pane card layout with a custom `#7AAACE` primary color scheme and responsive side banners.
    2. **UI Enhancements:** Applies interactive CSS hover effects to the main authentication cards for improved user feedback.
    3. **Breadcrumb Removal:** Conditionally hides the global breadcrumb for authentication routes (`login` and `register`) in the main client layout to maintain a clean interface.
    4. **Integration:** Maintains full compatibility with existing Google Social Login and Google reCAPTCHA v2 integrations.

### 4.5 Admin Back Navigation (REQ-166)
- **What (Business Purpose):** Standardizes navigation in the admin panel by providing intuitive "Back" buttons on detail pages and dashboard-linked index pages, reducing clicks and improving user workflow.
- **How it Works (Technical Flow):**
    1. **Show Pages:** All standard "show" (details) views are updated with a "Back" button that uses the `route()` function to return to the relevant index page.
    2. **Index Pages:** Primary index pages commonly accessed from the dashboard (Orders, Products, Customers, Best Selling, Low Stock) include a "Back to Dashboard" button.
    3. **UI Standard:** All buttons use Bootstrap 5 secondary styling (`btn-secondary btn-sm`) with a `bx bx-arrow-back` icon for visual consistency across the entire admin suite.

### 4.6 Standardized Admin Form Headers (REQ-167)
- **What (Business Purpose):** Standardizes the layout of all "Create" and "Edit" forms in the admin panel to provide a consistent user experience and easy navigation back to the list view.
- **How it Works (Technical Flow):**
    1. **Title Migration:** Page titles (e.g., "Add Product", "Edit Category") are moved from the `card-header` to a dedicated `d-flex` header section above the card.
    2. **Back Navigation:** Every form now includes a "Back" button in the top-right of the page header, linking to the relevant index page or dashboard.
    3. **Clean Interface:** Redundant `card-header` elements were removed or repurposed to ensure a clean, modern aesthetic that aligns with the "show" and "index" page standards established in REQ-166.

### 4.7 Sidebar Active State Logic (REQ-168)
- **What (Business Purpose):** Enhances admin panel usability by providing visual feedback on the user's current location within the system, ensuring the sidebar reflects the active module and maintains the visibility of sub-menu items.
- **How it Works (Technical Flow):**
    1. **Dynamic Active Classes:** Uses Laravel's `Request::routeIs()` and `Request::is()` helpers to conditionally apply the `active` class to navigation links based on the current route and its parameters.
    2. **Persistence of Collapsibles:** Automatically applies the `show` class and sets `aria-expanded="true"` on parent menu containers if any of their child routes are active, preventing the menu from collapsing when navigating within a module.
    3. **Wildcard Matching:** Leverages route wildcards (e.g., `admin.products.*`) to ensure parent menu items remain highlighted even on deep detail, create, or edit pages.

### 4.8 Simplified Admin Navigation (REQ-169)
- **What (Business Purpose):** Further refines the admin user interface by standardizing the text of navigation buttons, reducing visual noise and providing a cleaner, more focused user experience.
- **How it Works (Technical Flow):**
    1. **Text Normalization:** All "Back" buttons across all admin modules (Index, Show, Create, Edit) have been simplified to use exactly the text "Back".
    2. **Icon Preservation:** Maintains the `bx bx-arrow-back` icon for intuitive visual recognition while removing redundant contextual suffixes like "to Dashboard" or "to List".
    3. **Universal Consistency:** This standard is applied across Catalog, Inventory, Fulfillment, Management, and Settings modules to ensure a predictable UI pattern.

### **6. Admin Profile Management**
- **What:** Dedicated profile management for administrators to update their personal information and credentials.
- **How it Works:**
    - Admins can access their profile via the user dropdown in the topbar.
    - The profile view displays current details and read-only role information.
    - The edit form allows updating Name, Email, Avatar, and Password.
    - **Security:** Role selection is strictly excluded from this module to prevent privilege escalation by logged-in users.
- **Data & Storage:**
    - Uses the `admins` table.
    - Avatar images are stored in `storage/app/public/admins/`.
    - `AdminProfileService` handles the business logic and file operations.

### **7. Idempotent Seeders**
- **What:** Database seeders refactored to support multiple executions without data duplication or constraint failures.
- **How it Works:**
    - `ProductSeeder.php` refactored to remove destructive `delete()` calls. It now checks for existing product slugs and uses `updateProduct()` if found, or `storeProduct()` if new.
    - All other seeders (`Admin`, `Brand`, `Category`, `Supplier`, `Warehouse`, `Coupon`, etc.) utilize `updateOrCreate()` or `findOrCreate()` to maintain data integrity across multiple runs.
- Data & Storage:
    - Seeders interact with their respective Eloquent models using unique identifiers (email, slug, code, name) as lookup keys.

### **8. Policy & FAQ Management (REQ-172)**
- **What:** Separate management for legal policies (Privacy, Return) and a dedicated FAQ CRUD module.
- **How it Works:**
    - **Policies:** Admin can edit Privacy and Return policies via Summernote rich-text editors. Data is stored in the `policy_settings` table.
    - **FAQs:** A full CRUD module allows admins to manage frequently asked questions. FAQs can be activated/deactivated and ordered.
    - **Client Integration:** Public pages `/privacy-policy`, `/return-policy`, and `/faq` display this content. FAQs are presented in a Bootstrap accordion for a traditional user experience.
- **Data & Storage:**
    - `policy_settings` table: `privacy_policy` (longtext), `return_policy` (longtext).
    - `faqs` table: `question` (string), `answer` (text), `is_active` (boolean), `sort_order` (integer).

### **9. UI Button Standardization (REQ-174, REQ-221)**
- **What:** A consistent pattern for administrative action buttons and row numbering to ensure a clean, modern, and mathematically accurate interface.
- **How it Works:**
    - **Action Buttons in Tables:** "View", "Edit", "Details", and "Delete" buttons in all listing tables are now **icon-only** with Bootstrap tooltips for context.
    - **Consistent Icons:** 
        - View/Details: Eye icon (`solar:eye-broken` or `bx-show`).
        - Edit: Pen icon (`solar:pen-2-broken` or `bx-edit`).
        - Delete: Trash icon (`solar:trash-bin-trash-broken`).
    - **Table Row Numbering:** All administrative tables use `HelperClass::indexNumberSerialization($data)` to initialize the starting serial number and `{{ $sl++ }}` to increment it. This ensures accurate row numbering even across paginated results.
    - **Exceptions:** "Back" buttons, "Create/Add New" buttons, and the "View All Notification" link maintain their text for navigation prominence.
- **Standards:** All new modules must follow the `icon-only + tooltip` pattern for row-level actions and the `$sl++` pattern for serial numbers.

### **10. Image Validation Standardization (REQ-175)**
- **What:** Improved user experience for file upload errors by removing technical debt (indices) and using human-readable labels.
- **How it Works:**
    - **Removal of Indices:** Error messages for multiple image uploads (arrays) no longer include indices like `.0` or `.1`.
    - **Label Mapping:** Utilized the `attributes()` method in Form Requests to map technical database field names (e.g., `primary_image`, `gallery_images.*`, `dark_logo`) to user-friendly labels (e.g., "Primary Image", "Gallery Image", "Dark Logo").
    - **Descriptive Messages:** Custom validation messages in the `messages()` method provide clear guidance, such as "One or more gallery images exceed the 2MB size limit. Please compress and try again."
    - **Affected Modules:** Products, Sliders, General Settings, Categories, Brands, Client Returns, and Admin User Management.

### **11. Customer Reports & Analytics (REQ-173)**

- **Purpose:** Provide deep insights into customer behavior, retention, and lifetime value to drive data-driven marketing decisions.
- **How it Works:**
    - **Overview Dashboard:** Real-time metrics for total, new, returning, and active customers.
    - **Customer Analytics List:** Filterable list of all customers with aggregate order counts and total spend using **FlexSearch**.
    - **RFM Analysis:** Quantitatively segments customers based on Recency, Frequency, and Monetary value into VIP, Loyal, At Risk, and Lost.
    - **Purchase Behavior:** Visual analysis of order status distribution and AOV trends using ApexCharts.
    - **Cohort Analysis:** A retention heatmap tracking user activity grouped by registration month over a 6-month window.
    - **CLV Projections:** Predictive analysis of future customer value based on historical behavior.
    - **Guest Customer Tracking (REQ-199):** Identifies and counts unique guest shoppers based on email addresses from orders where `user_id` is null, providing a more complete picture of the total customer base beyond registered users.
    - **Standardized Action Buttons:** Primary actions (View, Edit, Delete, Add) are now icon-only with tooltips for a cleaner UI.
    - **Route Verification Mandate:** All Blade routes must be verified using `php artisan route:list` before implementation to prevent fictional route errors.

- **Detailed Formulas & Calculations:**
    - **Average Order Value (AOV):** `Total Revenue / Total Number of Orders`.
    - **Recency:** `Current Date - Last Order Date (in days)`.
    - **Frequency:** `Total count of non-cancelled orders`.
    - **Monetary:** `Sum of total_amount for all non-cancelled orders`.
    - **Customer Lifespan (Projection Baseline):** 24 Months.
    - **Monthly Purchase Frequency:** `Total Orders / Total Months since first order`.
    - **Predictive CLV:** `Historical Spend + (AOV × Monthly Purchase Frequency × 24)`.

- **Technical Implementation:**
    - **Service:** `CustomerReportService` handles all complex SQL aggregations.
    - **Controller:** `CustomerReportController` (Thin Controller).
    - **Visualization:** **ApexCharts** for interactive charts and heatmaps.
    - **Navigation:** Added to the "Reports" section in the Admin Sidebar.

- **Frontend Action Enhancements (REQ-219):**
    - **Stock-Aware Add to Cart:** Real-time stock validation for "Add to Cart" buttons. Disables the button and shows "OUT OF STOCK" when products or specific variants are unavailable, preventing invalid cart additions.

- **Customer Profile Management (REQ-202, REQ-212):**
    - **Service Layer:** `CustomerProfileService` centralizes all logic for updating user records, handling password hashing, and validating current credentials.
    - **UI Architecture:** A beautiful, unified design featuring a clean sidebar on desktop and a vertical stack on mobile. Built with standard Bootstrap 5 components for maximum structural stability and visual consistency.
    - **Social Login Integration:** Specifically addresses users who registered via Google Auth by allowing them to "set" their first password without requiring a non-existent "Current Password".
    - **Password Visibility Toggle (REQ-203):** Standardized implementation of "Show Password" checkboxes across all security forms.

