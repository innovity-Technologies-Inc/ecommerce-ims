# smart-ecom Requirements Overview

This document lists the high-level requirements for the modules implemented in the smart-ecom project.

## 1. Authentication & Security
- [x] **REQ-01:** Admin Login & Dashboard Access.
- [x] **REQ-02:** Client (Customer) Registration & Login.
- [x] **REQ-03:** Multi-Guard Session Management (Admin vs. User).
- [x] **REQ-04:** Profile Management for both Admins and Customers.
- [x] **REQ-68:** Social Login Icon Update (Replace text-heavy Google login button with a modern icon-only or icon-dominant layout on the client login page).

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
- [x] **REQ-19:** Dynamic SMTP Mail Configuration (DB-driven).
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
- [x] **REQ-34:** Dynamic App Configuration (App Name and Mail From Name loaded from General Settings).
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
- [x] **REQ-67:** Coupon Usage History (Dedicated administrative page to track detailed audit trails of specific coupon applications, including user and order data).








