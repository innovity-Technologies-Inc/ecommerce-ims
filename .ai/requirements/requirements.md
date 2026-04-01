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
- [ ] **REQ-88:** Purchase Order (PO) Management: Admin can create POs by selecting multiple products/variants and aligning them with a supplier.
- [ ] **REQ-89:** PO Email Notification: Option to automatically send the PO details to the supplier's email via a "Notify by Mail" checkbox during creation/update.
- [ ] **REQ-90:** PO Status Workflow: Support for `Draft`, `Sent`, and `Delivered` statuses with the ability for admins to transition between them.
- [x] **REQ-91:** PO Item Tracking: Maintain a detailed record of products, variants, quantities, and unit costs within each Purchase Order.
- [x] **REQ-92:** Mandatory Error Logging & SQL Fix: Implement mandatory logging for all `catch` blocks across the system to prevent hidden SQL/system errors. Fix the "Column not found" error in PO receiving by executing pending migrations.
- [ ] **REQ-93:** PO Warehouse Selection: Add warehouse selection to PO creation.
- [ ] **REQ-94:** PO Receiving Refinement: Implement Batch IDs, Serial Numbers, and separate `batches` and `batch_serials` tables for accurate product tracking during receipt.
- [x] **REQ-95:** Quarantine Warehouse: Add `is_quarantine` flag to warehouses and create a "Quarantine" warehouse for damaged products.
- [x] **REQ-101:** Warehouse Index Refinement: Explicitly show the Quarantine flag in the warehouse list and add a dedicated filter to toggle between Normal and Quarantine warehouses. (NEW)
- [x] **REQ-102:** Inventory Report Consolidation: Merged Stock and Batch Tracking reports into a single Stock report with batch search capability. Created a dedicated Damaged Products report for quarantine inventory. (NEW)
- [ ] **REQ-103:** PO Module & Inventory Overhaul: Remove quarantine warehouse dependency. Restructure DB to use `batches`, `batch_products`, and `batch_serials`. Implement `product_status` (good, damaged, damaged_return) in serials. Update all inventory reports to focus on product-wise stock and batch details. (COMPLETED)
- [ ] **REQ-104:** Warehouse Stock Limit Separation: Move warehouse-specific minimum stock limits from `inventory_levels` to a dedicated `warehouse_stock_limits` table. Update product form to allow assigning specific limits via a modal. Update dashboard low-stock logic. (NEW)
- [ ] **REQ-96:** Stock Ledger Integration: Maintain a detailed stock ledger tracking all transactions and ensuring consistency across warehouse, product, and variant stock levels.
- [x] **REQ-97:** Inventory Stock Report: Implement a comprehensive stock level view showing product quantities across different warehouses with direct links to product details.
- [x] **REQ-98:** Batch Tracking Report: Implement a batch-wise inventory view allowing administrators to drill down into batch items and view individual physical serial numbers.
- [ ] **REQ-99:** Inventory Data Refinement: Add `supplier_id` to batches, update `batch_serials` status to 'in-stock'/'sold', and enhance `stock_ledgers` with `supplier_id`, `unit_cost`, and calculated `cost` (+/-).

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
- [ ] **REQ-100:** Remove Global Low Stock Setting (Remove system-wide `low_stock_limit` from general settings. Thresholds should be managed per-product (`min_stock_global`) and per-inventory-level (`min_stock_override`)).
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
- [ ] **REQ-73:** Flash Sale Refinement (Add dedicated `flash_discount_price` and `flash_discount_percentage` fields to products and variants. Implement a homepage Flash Sale section with a countdown timer and "View All" button).
- [ ] **REQ-74:** Homepage Section Refinement (Add 'Top Picks' section and ensure all homepage sections have independent visibility and content controls in the Admin Panel).
- [ ] **REQ-75:** Registration & Checkout Refinement (Simplify registration to only Email, Mobile, and Password. Update user profile automatically with checkout details if authenticated).
- [x] **REQ-76:** Bulk Product Upload (Import products and variants from Excel/CSV using Laravel Excel. Support for categories, brands, and base data excluding images).
- [ ] **REQ-77:** Return Module (Guest/Authenticated return requests - restricted to 'Delivered' orders only, Admin approval/rejection with damage/intact selection, receiving workflow, stock restoration for intact items, and wastage tracking for damaged items).
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
- [ ] **REQ-107:** Damage Entry (Warehouse Wastage) Module: Manually record products damaged within the warehouse. Includes warehouse/batch/product selection, serial number tracking (mandatory if applicable), and status updates (product_status = damaged, stock_status = wastage). Integrates with Stock Ledger (warehouse_damage) and Inventory Levels (decrements current, increments damaged). (NEW)
- [ ] **REQ-108:** Move Wastage Sidebar Menu: Move the "Wastages" menu item from the "Returns" section to the "Inventory" section in the admin sidebar for better categorization. (NEW)
- [ ] **REQ-109:** Disable Serial Number Range Logic: When receiving Purchase Orders, serial numbers containing hyphens (`-`) should be treated as literal serials instead of ranges, as the system now uses Select2 for individual tag inputs. (NEW)
- [x] **REQ-106:** Stock Adjustment Module: Manual stock entry system without PO. Includes batch creation, warehouse targeting, product/variant selection, optional serial tracking, and unit cost recording. Synchronizes with Batch, Batch Serials, Inventory Levels, and Stock Ledger. (NEW)
- [x] **REQ-105:** Supplier RMA Module (Return to Vendor): Admin can return damaged products to vendors. Selection of vendors/POs, batch/serial tracking, email notification toggle, and status workflow (Pending, Approved, Shipped, Closed). Stock ledger integration (RTV_Dispatch) and inventory updates upon closing. (NEW)
- [x] **REQ-67:** Coupon Usage History (Dedicated administrative page to track detailed audit trails of specific coupon applications, including user and order data).
- [ ] **REQ-116:** Supplier Performance Score: Calculate a performance score for each PO upon receipt (40% for on-time delivery, 60% for quality based on damaged quantity). Display the average score in the Supplier index.
- [ ] **REQ-117:** Supplier Details Page: Implement a comprehensive details view for suppliers showing vendor information and a list of all associated Purchase Orders.
R E Q - 1 1 2 :   R e m o v e   ' D e l i v e r e d '   s t a t u s   o p t i o n   f r o m   P u r c h a s e   O r d e r   c r e a t i o n   a n d   e d i t   f o r m s   t o   e n s u r e   i n v e n t o r y   t r a c k i n g   i n t e g r i t y .  
 R E Q - 1 1 3 :   R e f a c t o r   D a m a g e d   P r o d u c t s   r e p o r t   t o   p r i o r i t i z e   B a t c h   N u m b e r ,   r e m o v e   s a l e a b l e   q u a n t i t y ,   a n d   s h o w   g r a n u l a r   d a m a g e d   s e r i a l s   i n   d e t a i l s .  
 R E Q - 1 1 4 :   A d d   ' S t o c k   D e t a i l s '   b u t t o n   t o   W a r e h o u s e   i n d e x   t o   v i e w   g r a n u l a r   w a r e h o u s e - w i s e   i n v e n t o r y ,   b a t c h e s ,   a n d   s e r i a l s .  
 R E Q - 1 1 5 :   R e f a c t o r   A d m i n   S i d e b a r   t o   e l e v a t e   I n v e n t o r y   s u b m e n u s   t o   t o p - l e v e l   m e n u   i t e m s   f o r   b e t t e r   a c c e s s i b i l i t y .  
 