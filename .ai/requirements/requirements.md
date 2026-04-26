# smart-ecom Requirements Overview

This document lists the high-level requirements for the modules implemented in the smart-ecom project.

## 1. Authentication & Security
- [x] **REQ-01:** Admin Login & Dashboard Access.
- [x] **REQ-02:** Client (Customer) Registration & Login.
- [x] **REQ-03:** Multi-Guard Session Management (Admin vs. User).
- [x] **REQ-04:** Profile Management for both Admins and Customers.
- [x] **REQ-68:** Social Login Icon Update (Replace text-heavy Google login button with a modern icon-only or icon-dominant layout on the client login page).
- [x] **REQ-81:** Google reCAPTCHA Integration (Implement reCAPTCHA v2 for login and registration pages managed via .env).
- [x] **REQ-83:** Admin Avatar UI Standardization (Ensure all admin avatars in navbar, index tables, and forms have a fixed circular shape and size using CSS).
- [x] **REQ-84:** Admin Theme Color Customization (Update sidebar and dark theme colors to #001F3D).

## 11. Inventory Management
- [x] **REQ-85:** Create Locations (Warehouses): An admin creates a record in warehouses. warehouse should have name, location.
- [x] **REQ-86:** Onboard Vendors (Suppliers): An admin creates a record in suppliers. supplier should have name, email, mobile, address.
- [x] **REQ-87:** Global Minimum Stock: Add `min_stock_global` field to `products` table with a default value of `0` to track low stock alerts at the product level.
- [x] **REQ-88:** Purchase Order (PO) Management: Admin can create POs by selecting multiple products/variants and aligning them with a supplier.
- [x] **REQ-89:** PO Email Notification: Option to automatically send the PO details to the supplier's email via a "Notify by Mail" checkbox during creation/update.
- [x] **REQ-90:** PO Status Workflow: Support for `Draft`, `Sent`, and `Delivered` statuses with the ability for admins to transition between them.
- [x] **REQ-91:** PO Item Tracking: Maintain a detailed record of products, variants, quantities, and unit costs within each Purchase Order.
- [x] **REQ-92:** Mandatory Error Logging & SQL Fix: Implement mandatory logging for all `catch` blocks across the system to prevent hidden SQL/system errors. Fix the "Column not found" error in PO receiving by executing pending migrations.
- [x] **REQ-93:** PO Warehouse Selection: Add warehouse selection to PO creation.
- [x] **REQ-94:** PO Receiving Refinement: Implement Batch IDs, Serial Numbers, and separate `batches` and `batch_serials` tables for accurate product tracking during receipt.
- [x] **REQ-95:** Quarantine Warehouse: Add `is_quarantine` flag to warehouses and create a "Quarantine" warehouse for damaged products.
- [x] **REQ-101:** Warehouse Index Refinement: Explicitly show the Quarantine flag in the warehouse list and add a dedicated filter to toggle between Normal and Quarantine warehouses. (NEW)
- [x] **REQ-102:** Inventory Report Consolidation: Merged Stock and Batch Tracking reports into a single Stock report with batch search capability. Created a dedicated Damaged Products report for quarantine inventory. (NEW)
- [x] **REQ-103:** PO Module & Inventory Overhaul: Remove quarantine warehouse dependency. Restructure DB to use `batches`, `batch_products`, and `batch_serials`. Implement `product_status` (good, damaged, damaged_return) in serials. Update all inventory reports to focus on product-wise stock and batch details. (COMPLETED)
- [x] **REQ-104:** Warehouse Stock Limit Separation: Move warehouse-specific minimum stock limits from `inventory_levels` to a dedicated `warehouse_stock_limits` table. Update product form to allow assigning specific limits via a modal. Update dashboard low-stock logic. (NEW)
- [x] **REQ-96:** Stock Ledger Integration: Maintain a detailed stock ledger tracking all transactions and ensuring consistency across warehouse, product, and variant stock levels.
- [x] **REQ-97:** Inventory Stock Report: Implement a comprehensive stock level view showing product quantities across different warehouses with direct links to product details.
- [x] **REQ-98:** Batch Tracking Report: Implement a batch-wise inventory view allowing administrators to drill down into batch items and view individual physical serial numbers.
- [x] **REQ-99:** Inventory Data Refinement: Add `supplier_id` to batches, update `batch_serials` status to 'in-stock'/'sold', and enhance `stock_ledgers` with `supplier_id`, `unit_cost`, and calculated `cost` (+/-).

## 2. Catalog Management
- [x] **REQ-05:** Brand CRUD (Logo, Slug, Status).
- [x] **REQ-06:** Category & Subcategory Management (Parent/Child Hierarchy).
- [x] **REQ-07:** Slug Generation & Image Handling for Catalog Items.

## 3. Product & Inventory System
- [x] **REQ-08:** Product CRUD with Marketing Flags (New Arrival, Hot Deal, Featured).
- [x] **REQ-09:** Flexible Pricing Engine (Base Pricing vs. Variant Pricing).
- [x] **REQ-10:** Product Variant Management (Variant Name, SKU, Stock).
- [x] **REQ-11:** Multi-Image Gallery with Primary Image selection.

## 4. Customer Shop Frontend
- [x] **REQ-12:** Dynamic Product Listing (Grid & List views).
- [x] **REQ-13:** Advanced Sidebar Filtering (Category, Brand, Price Slider).
- [x] **REQ-14:** Global Navbar Search (FlexSearch integration).
- [x] **REQ-15:** Detailed Product View with Dynamic Variant Selection.

## 5. Wishlist & Personalization
- [x] **REQ-16:** Persistent Wishlist for Authenticated Users.
- [x] **REQ-17:** Accurate Pricing Logic for Wishlisted Items (Net Price calculation).

## 6. Site Settings & Configuration
- [x] **REQ-18:** General Settings (Logo, SEO, Currency, visual assets).
- [x] **REQ-100:** Remove Global Low Stock Setting (Remove system-wide `low_stock_limit` from general settings. Thresholds should be managed per-product (`min_stock_global`) and per-inventory-level (`min_stock_override`)).
- [x] **REQ-19:** SMTP Mail Configuration (Managed via .env).
- [x] **REQ-20:** Homepage Section Management (Visibility & Content Source for Bestsellers/Featured).

## 7. Shopping Cart System
- [x] **REQ-21:** Hybrid Cart Management (Database for Users / Session for Guests).
- [x] **REQ-22:** Cart UI Integration (Cart Page, Topbar Mini-cart, Mobile Navbar).
- [x] **REQ-23:** Cart Page UI Alignment (Banner height matched to Cart Total card).

## 8. Mobile UI Refinements
- [x] **REQ-24:** Mobile Navbar Alignment (Logo and Icons spacing fix).
- [x] **REQ-25:** Search Category Vertical Alignment (Fix for "All categories" text position).
- [x] **REQ-26:** Database Seeder Alignment (Fix seeders to match current model schemas and fields).
- [x] **REQ-27:** Cart Module Architectural Refactoring (Ensure full adherence to Service Layer and Form Request patterns).
- [x] **REQ-28:** Admin Management Architectural Refactoring (Refactor Admin CRUD to Service Layer and Form Request patterns).
- [x] **REQ-29:** Sidebar UI Refinement (Hide unlinked menus, link Users to Admin CRUD).
- [x] **REQ-31:** Admin Route Conflict Resolution (Rename public/admin to public/admin_assets to avoid 403 Forbidden errors).
- [x] **REQ-32:** Checkout System & Order Management (Checkout form, guest/user support, COD payment, Order status tracking, Admin order management, and Email notifications).
- [x] **REQ-33:** Shipping Method Module & UI Integration (Admin CRUD, Cart selection with dynamic price update, and Checkout display).
- [x] **REQ-34:** App Configuration (App Name and Mail From Name managed via .env).
- [x] **REQ-35:** Order History & Tracking (Authenticated user history and guest order tracking by ID).
- [x] **REQ-36:** Invoice Management Module (Admin invoice generation, regeneration, and client-side download via JS Print).
- [x] **REQ-37:** Contact Settings Module (Admin settings for company name, email, address, phone number, integrated into invoices).
- [x] **REQ-38:** Customer Management Module (Admin panel for managing authenticated users: list, profile, purchase history, status toggle, and deletion).
- [x] **REQ-39:** Map Integration in Contact Settings (Add map link field to contact settings for frontend display).
- [x] **REQ-40:** Dynamic Contact Page (Replace static content in client contact page with dynamic data from database).
- [x] **REQ-41:** Dynamic Social Links (Manage social media URLs and visibility toggles in contact settings, integrated into frontend).
- [x] **REQ-42:** Contact Message Management (Client-side form submission, DB storage, email confirmation, and Admin panel listing).
- [x] **REQ-43:** Pagination Info (Add "Showing X to Y of Z Results" text to all index pages' pagination areas).
- [x] **REQ-44:** Contact Message Detail View (Individual page for viewing a single contact message in admin panel).
- [x] **REQ-45:** Admin Profile Image (Admin should be able to upload and update their profile image).
- [x] **REQ-46:** Product Status (Active/Discontinued toggle for products with client-side red badge display).
- [x] **REQ-47:** Admin Live Search/Filter/Sort (AJAX-based live searching, filtering, and sorting using laravel-flexsearch across all admin index pages).
- [x] **REQ-48:** Advanced Order Filtering (Filter orders by Status, Payment Method, Payment Status, and Date Range in admin index).

## 9. Promotions & Marketing
- [x] **REQ-49:** Coupon Management Module (Admin CRUD, code generation, discount logic, usage limits, and date range filtering).
- [x] **REQ-50:** Client-side Coupon Application (AJAX-based application on checkout page, instant price updates).
- [x] **REQ-51:** Coupon Usage Tracking (History table tracking User ID, Email, Name, and total usage limits).
- [x] **REQ-69:** Automated Flash Sale Expiry (Automatically reset product discounts to 0 when a flash sale expires based on its end date via a scheduled console command).
- [x] **REQ-70:** Accurate Price Sorting (Ensure Shop page sorting by price correctly handles hybrid products with variants and base prices).
- [x] **REQ-71:** Global Wishlist Functionality (Fix wishlist button on homepage and centralize logic to prevent redundancy).
- [x] **REQ-72:** Wishlist Add-to-Cart (Ensure "Add to Cart" button on the wishlist page correctly handles variants and triggers AJAX for simple products).
- [x] **REQ-73:** Flash Sale Refinement (Add dedicated `flash_discount_price` and `flash_discount_percentage` fields to products and variants. Implement a homepage Flash Sale section with a countdown timer and "View All" button).
- [x] **REQ-74:** Homepage Section Refinement (Add 'Top Picks' section and ensure all homepage sections have independent visibility and content controls in the Admin Panel).
- [x] **REQ-75:** Registration & Checkout Refinement (Simplify registration to only Email, Mobile, and Password. Update user profile automatically with checkout details if authenticated).
- [x] **REQ-76:** Bulk Product Upload (Import products and variants from Excel/CSV using Laravel Excel. Support for categories, brands, and base data excluding images).
- [x] **REQ-77:** Return Module (Guest/Authenticated return requests - restricted to 'Delivered' orders only, Admin approval/rejection with damage/intact selection, receiving workflow, stock restoration for intact items, and wastage tracking for damaged items).
- [x] **REQ-78:** Role-Based Access Control (RBAC) Module (Implement spatie/laravel-permission, Role CRUD management, assign roles to admin users, and profile image support for admins).
- [x] **REQ-79:** Permission Management (UI to create permissions grouped by Menu Name and Operations, stored as menu.operation. Role form integration with grouped checkboxes and "Check All" logic).
- [x] **REQ-80:** Order Cancellation/Rejection Remarks (Add reason/remarks field for cancelled or rejected orders. Remarks visible in tracking and included in status update emails).

## 10. Flash Sale Module
- [x] **REQ-52:** Flash Sale Management (Single edit form for managing active/inactive status and global configuration).
- [x] **REQ-53:** Optimized Product Selection (Paginated and searchable product list using FlexSearch for flash sale inclusion).
- [x] **REQ-54:** Dynamic Discount Synchronization (Automatically apply/remove discounts to products and variants based on flash sale status or product removal).
- [x] **REQ-55:** Flash Sale Tracking (Maintain a dedicated table for flash sale metadata and product associations).
- [x] **REQ-56:** Advanced Flash Sale Filtering (Add Brand, Category, Subcategory, and Sort filters to the product selection panel).
- [x] **REQ-57:** Optimized Flash Sale Product Loading (Ensure products load on initial page load with pagination and fix image display issues).
- [x] **REQ-58:** Client-side Flash Sale Filter (Add a filter to the shop page to filter products by Flash Sale title using FlexSearch).
- [x] **REQ-59:** Advanced Price Filtering (Variant-aware, discount priority, and handling of products with 0/NULL base prices).
- [x] **REQ-60:** Frontend Architectural Refactoring (Implementation of Thin Controllers, Service Layer, and Form Requests for the Frontend module).
- [x] **REQ-61:** Public Invoice Access (Ability to print invoices from the Order Tracking page without authentication).
- [x] **REQ-62:** Optimized Variant Display (Intelligent formatting of variant details in Cart, Mini-Cart, and Checkout to handle missing size/color attributes gracefully).
- [x] **REQ-63:** Product Stock Display (Detailed stock availability on Product Details pages for both Client and Admin interfaces, supporting both base and variant-wise quantities).
- [x] **REQ-64:** Automated Stock Management (Automatic decrement of stock upon order placement and restorative increment upon order cancellation/rejection).
- [x] **REQ-65:** Order Status Finality (Prevent further status changes once an order is set to 'Delivered', 'Cancelled' or 'Rejected').
- [x] **REQ-66:** Status Reuse Restriction (Prevent an order from being moved to any status it has previously held in its lifecycle).
- [x] **REQ-107:** Damage Entry (Warehouse Wastage) Module: Manually record products damaged within the warehouse. Includes warehouse/batch/product selection, serial number tracking (mandatory if applicable), and status updates (product_status = damaged, stock_status = wastage). Integrates with Stock Ledger (warehouse_damage) and Inventory Levels (decrements current, increments damaged). (NEW)
- [x] **REQ-108:** Move Wastage Sidebar Menu: Move the "Wastages" menu item from the "Returns" section to the "Inventory" section in the admin sidebar for better categorization. (NEW)
- [x] **REQ-109:** Disable Serial Number Range Logic: When receiving Purchase Orders, serial numbers containing hyphens (`-`) should be treated as literal serials instead of ranges, as the system now uses Select2 for individual tag inputs. (NEW)
- [x] **REQ-106:** Stock Adjustment Module: Manual stock entry system without PO. Includes batch creation, warehouse targeting, product/variant selection, optional serial tracking, and unit cost recording. Synchronizes with Batch, Batch Serials, Inventory Levels, and Stock Ledger. (NEW)
- [x] **REQ-105:** Supplier RMA Module (Return to Vendor): Admin can return damaged products to vendors. Selection of vendors/POs, batch/serial tracking, email notification toggle, and status workflow (Pending, Approved, Shipped, Closed). Stock ledger integration (RTV_Dispatch) and inventory updates upon closing. (NEW)
- [x] **REQ-67:** Coupon Usage History (Dedicated administrative page to track detailed audit trails of specific coupon applications, including user and order data).
- [x] **REQ-116:** Supplier Performance Score: Calculate a performance score for each PO upon receipt (40% for on-time delivery, 60% for quality based on damaged quantity). Display the average score in the Supplier index.
- [x] **REQ-117:** Supplier Details Page: Implement a comprehensive details view for suppliers showing vendor information and a list of all associated Purchase Orders.
- [x] **REQ-118:** Order Inventory Selection: Connect order flow with inventory. When status is changed to 'Shipped', allow selection of specific warehouses, batches, and serial numbers. Update stock status to 'Shipped' and then to 'Sold' upon delivery, with corresponding stock ledger entries.
- [x] **REQ-119:** Inventory Costing Refinement: Move `unit_cost` from `products`, `product_variants`, and `stock_ledgers` to `batch_products`. Remove `cost` from `stock_ledgers`. Update Admin UI to show `unit_cost` in Batch details instead of Product details.
- [x] **REQ-120:** Granular Stock Ledger Entries: Ensure that stock movements for serial-tracked items are logged as individual entries (change_qty: 1 or -1) per serial number in the `stock_ledgers` table. Added `batch_serial_id` to `stock_ledgers`. (UI Table removed per user request).
- [x] **REQ-121:** Advanced Order Inventory Processing: Implement `ordered_product_batches` table to track multiple batches per order item. Add `total_cost` to `orders` and `order_items` tables. Update Shipped status workflow to support multi-batch selection, automated procurement cost calculation, and aggregate stock ledger entries.
- [x] **REQ-122:** Advanced Return Inventory Processing: Implement batch and serial selection for returns. For 'intact' returns, increase stock levels across products, variants, batches, batch_products, and inventory_levels. Update ordered quantities in orders, order_items, and ordered_product_batches. For 'damaged' returns, mark serials as damaged and move to wastage.
- [x] **REQ-123:** Multiple Image Uploads for Returns: Enable clients to upload multiple images when requesting a return. Store images in a dedicated `return_images` table and display them in the admin return request details view.
- [x] **REQ-124:** Return Request UI & Logic Fix: Expand the return approval form to full-width. Ensure batch and serial selection only shows items originally shipped to the customer for that specific order.
- [x] **REQ-125:** Sales Reporting Module: Comprehensive sales metrics including gross/net sales, costs, profit, and margins with dynamic grouping (daily/weekly/monthly/yearly) and deep filtering by warehouse, products, and variants.
- [x] **REQ-126:** Inventory Reporting Module: Comprehensive inventory level and valuation reports with "As-of date" support, filtering by warehouse, supplier, product/category/brand, and batch, including valuation breakdowns.
- [x] **REQ-127:** Stock Reporting Module: Detailed stock reports including movement history, batch aging, damaged stock, and serial tracing. Features include warehouse-specific stock levels, low-stock alerts, and valuation metrics.
- [x] **REQ-128:** Stock Report Calculation Fix: Correct the stock calculation discrepancy in the Stock Report by ensuring the join between `inventory_levels` and `batch_products` includes `product_variant_id` to prevent row duplication. Align with Inventory Valuation logic.
- [x] **REQ-129:** Warehouse Performance Report: Implement a comprehensive warehouse efficiency and quality metrics report. KPIs include stock movements (Opening, Received, Sold, Adjusted, Damaged, RTV, Closing), Inventory Value, Fulfillment metrics (Fill rate, Damage rate, Stock turnover), and Operational metrics (Slow-moving stock %, Low-stock SKU count). Requires integration with `stock_ledgers`, `inventory_levels`, and `order_items`.
- [x] **REQ-130:** Standardize Product Variant Stock Defaults: Update `product_variants` table to ensure the `stock` column has a default value of `0` and is `NOT NULL`, matching the `products` table schema for consistency and to prevent calculation errors.
- [x] **REQ-131:** Warehouse Performance Report Export & Print: Implement "Excel Export" and "Print" functionality for the Warehouse Performance report (both index and detail views). Align UI and logic with existing Sales and Inventory reports, utilizing the `ReportService` and `WarehousePerformanceService`.
- [x] **REQ-132:** Warehouse Performance Gross Fill Rate Logic Update: Update the calculation logic for "Gross Fill Rate" in the Warehouse Performance Report to use the `stock_ledger` table as the primary source of truth for total units ordered and fulfilled.
- [x] **REQ-133:** Low Stock Notifications & Automation: Implement a system to monitor low stock at global and warehouse levels, display alerts in the dashboard, and send automated email notifications to a designated address configured in General Settings. Includes a background scheduler and restock quantity suggestions.
- [x] **REQ-134:** Seeder Fixes & Product Alignment: Fix all existing seeders to ensure they run without errors (e.g., duplicate entries, missing fields) and align the `ProductSeeder` logic with the current product creation form and service.
- [x] **REQ-135:** Product Image Upload Refinement: Update image upload logic to allow max 2MB per image, limit gallery uploads to 5 images at a time, and introduce a dedicated "Primary Image" field separate from the "Gallery Images" field.
- [x] **REQ-136:** Dashboard Revenue Analytics: Implement revenue tracking cards (Daily, Weekly, Monthly, Yearly) and interactive charts (Revenue vs Cost, Daily Sales) on the admin dashboard. Revenue is calculated from the `orders` table using `total_amount` (Sale), `total_cost` (Cost), and derived profit.
- [x] **REQ-137:** Aesthetic Admin Login UI: Redesign the admin login page with a stunning, modern dark theme aesthetic, featuring glassmorphism, glowing accents, and high-quality visual elements.
- [x] **REQ-138:** Homepage Section Product Selector UI: Standardize the product selection interface for homepage sections (Best Sellers, Hot Deals, etc.) to match the AJAX-driven, dual-column UI used in the Flash Sale module.
- [x] **REQ-139:** Full Data Report Printing: Update the print functionality in all administrative reports (Sales, Inventory, Stock, Warehouse Performance) to ensure that clicking "Print" captures all available data rows instead of just the currently paginated page.
- [x] **REQ-140:** Stock Report View-All Refinement: Implement full-data "Print" and "Excel Export" for all detailed stock report views (Warehouse, Product, Batch, Movement, Aging, Wastage, Serial). Ensure Excel exports match the current view's data and filters instead of defaulting to the main stock report. Remove redundant filters and include proper headers in printed versions.
- [x] **REQ-141:** Fix Excel Exports: Ensure all Excel exports in ReportController retrieve full data regardless of the active UI page by explicitly passing `null` for `perPage` limits and updating default pagination values in ReportService.
- [x] **REQ-142:** Report Filter Preservation: Update Sales, Inventory, and Stock report filter forms to include a hidden `view` parameter, ensuring users remain on the "View All" detailed page when applying filters instead of being redirected back to the dashboard.
- [x] **REQ-143:** Excel Report Blank Normalization: Ensure all Excel reports (Sales, Stock, Inventory, Warehouse Performance) replace null or empty values (including whitespace-only strings) with a literal `0` in the exported file to prevent any blank cells from appearing in Excel.
- [x] **REQ-144:** Report View Data Normalization: Ensure all UI-based reports (Warehouse Performance, Sales, Stock, Inventory) replace null or empty values with `0` (for numeric fields) or appropriate placeholders (for text fields) to ensure no blank cells appear in the browser.
- [x] **REQ-145:** Dashboard & Report Tooltips: Add Bootstrap 5 tooltips to all metric cards and table headers across the main dashboard and reporting modules, providing clear "what" and "how" (calculation logic) explanations for every KPI.
- [x] **REQ-146:** Product Form UX Improvements: Customize image upload error messages for better clarity when files exceed 2MB. Add a guidance note under the "Main Description" (SummerNote) field advising administrators to use content from Google Docs or Microsoft Word for optimal formatting.
- [x] **REQ-147:** Return Module Enhancements: Implement automated email notifications for return request confirmation (customer) and status updates (admin). Shift the inventory allocation and condition inspection logic from the "Approval" step to the "Receiving" step to ensure accurate physical verification before stock adjustment.
- [x] **REQ-148:** Stock Report UI Enhancement: Add "Export" buttons next to the "Print" buttons in all dashboard cards of the Stock Reports to provide granular Excel export capabilities for Warehouse, Product, Aging, Wastage, Movement, and Serial Trace data.
- [x] **REQ-149:** Report Data Accuracy (Print/Export): Ensure that "Print" and "Export" buttons in all report dashboards (Sales, Stock, Inventory) always process the complete filtered dataset instead of being limited to the 10 rows shown in the dashboard's preview.
- [x] **REQ-150:** Report UI Optimization: Remove the "Batch #" search filter from the "Warehouse-wise Valuation" inventory report to ensure the interface is focused only on relevant aggregate filters for warehouse-level data.
- [x] **REQ-151:** Report UI Optimization: Remove the "Batch #" search filter from the "Product-wise Valuation" inventory report to ensure the interface is focused only on relevant aggregate filters for product-level data.
- [x] **REQ-152:** Pricing & Discount Logic Fix: Implement robust price and discount fallbacks for products with variants using global pricing. Ensure cart, checkout, and mini-cart correctly fall back to product-level regular and discount prices when variant-specific prices are missing. Added display of regular prices beside discounted prices in all cart/checkout views.
- [x] **REQ-153:** Available Coupons Modal: Add an "Available Coupons" feature on the checkout page that opens a modal showing all active coupons. Eligible coupons (based on current subtotal) are selectable, while ineligible ones are greyed out with a specific reason (e.g., minimum spend not met). Applying a coupon from the modal automatically fills the input and triggers the application logic.
- [x] **REQ-154:** Mandatory Source Control Guideline: Update the project's development workflow guidelines to require a mandatory stage and commit step with a clear, task-referenced message after every single task completion and verification.
- [x] **REQ-155:** Order Confirmation Email Refinement: Update the customer order confirmation email template to include a detailed financial breakdown (Gross Subtotal, Product Discounts, Coupon Discounts, and Shipping Charges) matching the professional layout of the generated invoices.
- [x] **REQ-156:** Purchase Order Schema Cleanup: Remove the redundant `batch_number` column from the `purchase_orders` table as batch tracking is managed by the dedicated `Batch` model.
- [x] **REQ-157:** PO Module Currency Standardization: Add the global currency symbol (from general settings) to all amount values in the Purchase Order module, including the list table, show details, create/edit forms, and supplier emails.
- [x] **REQ-158:** Warehouse Schema Optimization: Remove the `is_quarantine` flag from the `warehouses` table and update the `WarehouseSeeder` to remove the Quarantine warehouse, simplifying the inventory structure.
- [x] **REQ-159:** Sales Report Data Accuracy: Force the Sales Report to only calculate data from orders with a 'Delivered' status to ensure financial reporting accurately reflects finalized sales. Removed the misleading "Order Status" filter from the Sales Report UI.
- [x] **REQ-160:** Low Stock Alert Documentation: Add a comprehensive technical breakdown of the low-stock notification system to the project documentation, including detection logic, anti-spam mechanisms, and automated scheduling.
- [x] **REQ-161:** Non-Technical User Guide: Create a detailed operational guide (USER_GUIDE.md) that explains the end-to-end business flow of the application for non-technical users, covering inventory setup, procurement, order fulfillment, and reporting.
- [x] **REQ-162:** Granular Return Condition Logic: Move the "Item Condition" selection from the product level to the individual allocation (split) level during the Physical Receiving phase. This allows admins to accurately record different conditions (Intact/Damaged) for items of the same product within a single return request.
- [x] **REQ-163:** Admin Notification System: Implement a custom database-driven notification system for administrators. Notifications are automatically triggered for Low Stock events, New Orders, Return Requests, and Contact Messages. Includes a dynamic navbar dropdown, unread count tracking, a "View All" page with advanced filtering (Type, Search, Date Range), and one-click "Mark as Read" functionality.
- [x] **REQ-164:** Client Auth Pages Redesign: Redesign the client-side login and registration pages with a modern, aesthetically pleasing layout. Remove the breadcrumb from these pages and ensure visual consistency with the project's primary color scheme (#7AAACE).
- [x] **REQ-165:** Dynamic Auth Banners: Add functionality in the Admin General Settings to upload custom banners for the Login and Registration pages. These banners are displayed dynamically on the authentication pages with a professional overlay and pattern fallback.
- [x] **REQ-166:** Admin Back Navigation: Add "Back" buttons to all detailed (show) pages and specific index pages linked from the dashboard (Orders, Products, Customers, Best Selling, Low Stock) to improve user navigation and experience.
- [x] **REQ-167:** Standardize Admin Create/Edit Headers: Move page titles from `card-header` to a new `d-flex` header section above the `card` and add a "Back" button across all specified admin forms (Brands, Categories, Coupons, Products, Inventory, etc.) to match the show/index page styles.
- [x] **REQ-168:** Sidebar Active State Logic: Implement dynamic `active` and `show` classes in the admin sidebar to highlight the current menu item and keep collapsible sections open when related sub-routes are active.
- [x] **REQ-169:** Simplify Admin "Back" Button Text: Simplify the text of all "Back" buttons in the admin panel to just "Back", preserving icons and maintaining consistency across all views.
- [ ] **REQ-170:** Dedicated Admin Profile Page: Implement a dedicated profile view and edit page for the logged-in administrator. The edit form must allow updating personal details (name, email, avatar, password) but strictly exclude role selection to prevent self-elevation or accidental lockout.
- [x] **REQ-171:** Idempotent Seeders: Ensure all database seeders use `updateOrCreate` or similar logic to allow multiple runs without failing or creating duplicate records.
- [x] **REQ-172:** Policy Pages & FAQ CRUD: Implement separate management for Privacy/Return policies using Summernote and a dedicated CRUD for FAQs. Create client-side pages and idempotent seeders.
- [ ] **REQ-173:** Comprehensive Customer Reports: Develop a new reporting module under the Admin Reports section for customer analytics. This includes an Overview Dashboard (stats), filtered Customer List, RFM Analysis (VIP, Loyal, At Risk, Lost), CLV (Customer Lifetime Value) calculations, Purchase Behavior (AOV, categories, trends), Cohort Analysis (retention), Churn Prediction, and detailed Segmentation. Must follow existing report design standards (filtering, export, print, visualization, tooltips).
- [x] **REQ-174:** Standardize Admin Action Buttons: Update all "View", "Edit", "Create", and "Details" buttons across the admin panel to display only icons (no text). Ensure consistent icon usage (e.g., eye icon for View/Details) and add this standard to the project guidelines.
- [x] **REQ-175:** CLV Projections Card Tooltip Fix: Remove the redundant central card tooltip from the "Customer Segmentation" card in the CLV Projections report while preserving individual segment (Whales, Medium, Standard) tooltips.
- [x] **REQ-176:** Customer Report AOV Card Styling: Update the "Average Order Value" card in the customer reports dashboard to have an emerald background matching the sidebar theme and ensure all text is solid white.
- [x] **REQ-178:** Comprehensive Database Seeding: Seed the database with 20 USA state-based warehouses, 15 suppliers, and 100 fashion products (with variants). For each product, download and upload at least 3 relevant fashion images. Generate Purchase Orders, Order Receives, and Stock Adjustments to populate initial stock and history. (NEW)
- [x] **REQ-180:** Scoped Unique Category Names: Ensure category names are unique only within the same parent. (NEW)
- [x] **REQ-181:** Unique Slug Generation: Implement robust slug generation to handle globally unique slugs even when names are duplicated across different parents. (NEW)
- [x] **REQ-182:** Hot Deal Layout: Update the Hot Deal section to display 4 cards per row on desktop and improve responsiveness. (NEW)
- [x] **REQ-184:** Aspect Ratio & Height Normalization: Standardize product card heights and image sizes using Aspect Ratio (1:1) and Flexbox normalization to handle varying content sizes. (NEW)
- [x] **REQ-185:** Image Size Note: Add a note in the product image upload section of the admin panel specifying the recommended image size (800x800 px) for better UI consistency. (NEW)
- [x] **REQ-186:** Featured Slider Responsive Fix: Fix the Featured Product slider to consistently show 2 columns on desktop resolutions, preventing oversized cards. (NEW)
- [x] **REQ-187:** Stock Adjustment Table Fix: Fix the Undefined variable $data error in the stock adjustment table partial. (NEW)
- [x] **REQ-188:** Flash Sale Discount Badge Fix: Resolve the issue where the discount badge was not showing for active flash sale products due to missing mass-assignment allowance in the Product model. (NEW)
- [x] **REQ-189:** Flash Sale Notification & Error Handling Fix: Standardize Flash Sale session messages and implement mandatory error logging in the controller. (NEW)
- [x] **REQ-190:** Cart & Wishlist Image Normalization: Standardize product image sizes in Cart and Wishlist views using Aspect Ratio (1:1) and object-fit cover for visual consistency with product cards. (NEW)
- [x] **REQ-192:** AVIF Image Support: Update all image upload sections (Products, Categories, Brands, Sliders, etc.) to accept the modern AVIF image format. (NEW)
- [x] **REQ-193:** Product Grid Spacing: Add vertical spacing (gutters) between product rows in the shop listing view to prevent cards from touching. (NEW)
- [x] **REQ-194:** Product List View Style Fix: Restore horizontal layout and internal padding for the list view, and fix the hover behavior for action buttons. (NEW)
- [x] **REQ-195:** PO Currency from General Settings: Update the Purchase Order module to use the currency symbol defined in General Settings dynamically instead of handcoded values. (NEW)
- [x] **REQ-196:** Stock Index Performance & UX: Fix flickering and blurring issues on the stock index page (especially in Firefox) during scrolling and AJAX updates. This includes optimizing background gradients by using fixed pseudo-elements, neutralizing problematic backdrop-filters, and implementing a stable loading overlay that prevents layout shifts. (NEW)
- [ ] **REQ-199:** Guest Customer Counter in Reports: Add a "Guest Customers" counter to the Customer Purchase Reports overview. Guest customers are identified by orders with no `user_id`, counted by unique email addresses. (NEW)
- [x] **REQ-200:** FlexSearch v4.0.0 Guidelines: Update project coding style guidelines with version-specific implementation patterns for FlexSearch v4.0.0, including multi-column filtering and relationship searching. (NEW)
- [ ] **REQ-201:** Split Customer Stats Rows: Reorganize the summary metrics in the Customer Reports overview to be displayed across two rows (3 + 2) for improved visual balance. (NEW)
- [ ] **REQ-202:** Social Login Password Fix: Allow users registered via Google/Social login to set/update their password without requiring a "Current Password" if one hasn't been set, and refactor the customer profile logic to use Service Layer and Form Requests. (NEW)
- [x] **REQ-203:** Show Password Toggle: Implement a "Show Password" checkbox on all password-related forms in both Admin and Client interfaces to toggle visibility of current, new, and confirmation password fields. (NEW)
- [ ] **REQ-204:** Show Password Toggle on Admin User Form: Add the "Show Password" checkbox to the Admin user creation and edit form (`resources/views/admin/users/forms.blade.php`). (NEW)
- [x] **REQ-205:** Modern Customer Profile UI: Redesign the customer account information page with a modern, sidebar-tabbed interface, improved typography, and consistent styling with the rest of the e-commerce frontend. (NEW)
- [ ] **REQ-206:** Responsive Profile Layout (Mobile Accordion): Update the profile page to behave like an accordion on mobile devices, where clicking a tab section opens the corresponding form immediately below the tab button, while maintaining the sidebar-tab layout on desktop. (NEW)
- [ ] **REQ-207:** Profile Mobile Spacing Fix: Resolve the issue where hidden tab panes on mobile were still occupying vertical space, causing large gaps between section buttons. Ensure hidden panes are fully removed from the layout until activated. (NEW)
- [ ] **REQ-208:** Profile Tab Toggle Behavior: Update the profile section navigation to allow "click-to-close" functionality. If an active tab is clicked again, it should collapse (close), behaving like a true accordion. (NEW)
- [ ] **REQ-209:** Client Master Layout Style Cleanup: Remove accidental Admin panel CSS overrides and undefined Bootstrap variables from the Client master layout to resolve topbar and breadcrumb style breakage. (NEW)
- [ ] **REQ-210:** Account Layout Structural Fix: Repair the broken account profile layout by fixing unclosed sections and implementing a more robust CSS Grid structure that allows the sidebar to remain styled while correctly positioning content panes on desktop. (NEW)
- [ ] **REQ-211:** Perfect Profile Desktop/Mobile Layout: Refine the CSS Grid strategy to ensure the profile page correctly renders as a sidebar-tabbed interface on desktop (with a stable white sidebar card) and an interleaved accordion on mobile. (NEW)
- [ ] **REQ-212:** Modern Unified Profile UI: Replace the complex responsive grid logic with a clean, stable, and beautiful unified design. Uses a standard sidebar on desktop and a vertical stack on mobile, ensuring topbar/breadcrumb stability and consistent modern aesthetics. (NEW)
- [x] **REQ-213:** Customer Profile Image Upload: Add an option for customers to upload/update their profile image. This includes an edit icon on the avatar, a Bootstrap modal for file selection, a database migration to add the `image` column to the `users` table, and backend logic for handling file uploads. (NEW)
- [x] **REQ-214:** Mobile Cart Icon Adjustment: Reduce the size of the cart icon and quantity badge in mobile view for better visual balance. (NEW)
- [x] **REQ-215:** Product Sort Mobile Wrapping Fix: Ensure the "Sort By" text and dropdown in the products page stay on a single line on mobile devices to prevent layout shifts. (NEW)
- [x] **REQ-216:** Order History "Start Shopping" Button Fix: Ensure the "Start Shopping" button in the empty order history view stays on a single line on mobile devices by preventing text wrapping. (NEW)
- [x] **REQ-217:** Cart Page Shopping Buttons Fix: Ensure the "Go to Shop" and "Continue Shopping" buttons on the cart page stay on a single line on mobile devices to prevent text wrapping and improve layout consistency. (NEW)
- [ ] **REQ-218:** Cart Empty State and Button Standardization: Ensure "Your cart is empty" stays on one line. Remove the redundant "Go to Shop" button. Make action buttons ("Continue Shopping", "Clear Cart", "Proceed to Checkout") visually similar in size. When the cart is empty, hide all action buttons except "Continue Shopping" and hide the cart total/banner section. (NEW)
- [x] **REQ-219:** Stock-Aware Add to Cart Button: Disable the "Add to Cart" button when a product or specific variant is out of stock. Prevent clicks on disabled buttons via JavaScript and maintain consistent visual feedback. (UPDATED)
- [ ] **REQ-220:** Order Status Transition Update: Allow transitions from 'Processing' to 'Shipped' (already exists) and 'Cancelled' (new). Ensure 'Cancelled' from 'Processing' correctly handles any necessary stock restoration if applicable (though stock is only deducted at 'Shipped' status).
- [x] **REQ-221:** Standardize Table Serial Numbers: Ensure all administrative tables use the `HelperClass::indexNumberSerialization` method correctly with the `$sl++` increment pattern to maintain accurate row numbering across paginated results. (NEW)
- [x] **REQ-222:** Admin Activity Tracking: Add `created_by` and `updated_by` fields (foreign keys to `admins` table) to all relevant database tables. Implement an automated way to populate these fields during admin operations using model observers or traits. (NEW)
- [ ] **REQ-223:** Global Currency Standardization: Replace all hardcoded currency symbols (e.g., "$") in Blade views, emails, and reports with the dynamic currency setting from `HelperClass::generalSettings()->currency`.
- [ ] **REQ-224:** Facebook OAuth Integration: Implement "Login with Facebook" functionality using Laravel Socialite, following the existing Google OAuth pattern for user creation and authentication.
- [ ] **REQ-225:** Dynamic Currency Replacement: Replace all remaining hardcoded `$` symbols with the dynamic currency setting from `HelperClass::generalSettings()->currency` in specific Blade templates and emails. (NEW)
- [ ] **REQ-226:** Dynamic Banners: Make 4 homepage banners and 1 cart page banner dynamic. Admin should be able to upload these from the admin panel with clear size instructions (330x315, 690x315, 1410x230, 690x550).
- [ ] **REQ-227:** HRM Module: Implement a basic HRM for appointed users (admins/staff). Includes time tracking toggle, daily logged-in time calculation, manual daily work hour entry, salary breakdown (type/amount in profile), and payslip generation with daily/weekly/monthly/range filtering. Uses dynamic currency from general settings.





- [ ] **REQ-228:** Always Show Clock In/Out Button: The Clock In/Out button in the admin header should always be visible to logged-in administrators, regardless of the `is_time_tracking` setting in their profile. (DONE)
- [ ] **REQ-229:** Bulk Payslip Generation: Admins can generate payslips for all employees at once for a specific date range with a generation title. Index shows generation batches, details show all individual payslips in that batch. (NEW)

## Other
- [x] **REQ-99:** Remove manual pagination info blocks from admin table partials to avoid duplication with Laravel's links() method.
