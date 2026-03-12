# smart-ecom Project Documentation

## 1. Project Overview
**smart-ecom** is a high-performance, modern e-commerce platform built with **Laravel 12**. It uses a dual-interface architecture: a comprehensive **Admin Panel** for business operations and a sleek, responsive **Customer Frontend** for shoppers. The core philosophy of this project is maintainability, achieved through strict adherence to the Service Layer pattern and modular architecture.

### Core Tech Stack
- **Backend:** PHP 8.3.8, Laravel 12 (Streamlined Structure)
- **Frontend:** Bootstrap 5, jQuery 3.6, standard JavaScript (No Tailwind CSS or Alpine.js)
- **Database:** MySQL / PostgreSQL (Eloquent ORM)
- **Authentication:** Laravel Breeze (Multi-auth: Admin & User guards)
- **Search:** FlexSearch (powered by `daiyanmozumder/laravel-flexsearch`)
- **PDF/Printing:** High-performance JS Print Engine (`window.print()`)

---

## 2. Architectural Standards & System Flow

### Request Lifecycle (The "Laravel Boost" Flow)
1. **Route:** User makes a request to a named route.
2. **Form Request:** Inputs are strictly validated via dedicated Form Request classes (e.g., `ProductRequest`). Inline validation in controllers is prohibited.
3. **Controller:** Thin controllers receive validated data, inject a **Service**, and call a method.
4. **Service:** Executes business logic, handles complex calculations, manages file uploads via `HelperClass`, and interacts with Eloquent Models.
5. **Model:** Performs DB operations, defines relationships, and casts attributes.
6. **Response:** Controller returns a Blade view or a structured JSON response.

### Centralized Helper (`App\HelperClass`)
A globally accessible class used for repetitive tasks and data retrieval. **MANDATORY:** Always use `HelperClass` for retrieving global settings (General, Contact, Brands, etc.) within Blade templates. Global view sharing via Service Providers is strictly prohibited to maintain performance and explicit data flow.
- **File Management:** `HelperClass::file_upload()` securely stores files and returns paths; `HelperClass::file_delete()` removes old assets to prevent server bloat.
- **Indexing:** `HelperClass::indexNumberSerialization($paginatedData)` ensures accurate row numbering across paginated tables.
- **Global Data:** `HelperClass::generalSettings()`, `HelperClass::contactSettings()`, `HelperClass::getCategories()`, etc., are the only approved methods for accessing site-wide data in views.

### Documentation Protocol
Every module or architectural change must be documented in this file before a task is considered complete. Documentation must include:
- **The "What":** A high-level description of the new feature.
- **The "How":** Technical implementation details including logic patterns, service-layer integration, and specific technologies.

---

## 3. Comprehensive Module Breakdowns

### 3.1 Authentication & Security
- **What:** Separate login systems for Administrators and Customers.
- **How it Works:** Implemented using Laravel Breeze with multiple authentication guards (`web` for customers, `admin` for administrators). 
- **Implementation Details:** 
  - Admins authenticate against the `admins` table and are redirected to the admin dashboard.
  - Customers authenticate against the `users` table and are redirected to the homepage or their account dashboard.
  - Middleware (`auth:admin`, `auth:web`) strictly protects routes, ensuring complete isolation of privileges.

### 3.2 Catalog Management (Brands & Categories)
- **What:** Management of the foundational hierarchical data (Brands and Categories) that products belong to.
- **How it Works:** 
  - **Brands:** Managed via standard CRUD. They include a logo and a unique URL slug.
  - **Categories:** Implements a Parent/Child hierarchy allowing for sub-categories. 
- **Implementation Details:** 
  - Slugs are automatically generated via Eloquent mutators or Service logic on creation.
  - Deleting a category safely handles child relationships (e.g., setting parent_id to null or cascading deletes, depending on schema constraints).

### 3.3 Product & Inventory System
- **What:** The core catalog engine supporting complex pricing, variants, and marketing flags.
- **How it Works:**
  - **Flexible Pricing Engine:** A product can have a `base_price` and multiple `ProductVariant` records. If variants exist, their specific pricing overrides the base price.
  - **Marketing Flags:** Boolean columns in the `products` table (e.g., `is_new`, `is_featured`, `is_hot_deal`) dictate where the product appears on the frontend.
  - **Multi-Image Gallery:** Handled via a dedicated `product_images` table, allowing infinite images per product with one designated as the primary thumbnail.
- **Implementation Details:** 
  - `ProductService` orchestrates the creation of the product, uploads the primary image, iterates through variant arrays to create `ProductVariant` rows (handling SKU and stock), and stores secondary images in the `product_images` table.

### 3.4 Customer Shop & Frontend Filtering
- **What:** The public-facing product catalog with advanced search and filtering capabilities.
- **How it Works:**
  - **Sidebar Filtering:** Users can filter by Categories, Brands, and Price. The backend captures these query parameters (e.g., `?category=electronics&min_price=100`) and dynamically builds Eloquent queries in `FrontendController`/`ProductService`.
  - **Global Search:** Powered by `FlexSearch`. When a user types in the navbar, the query is passed to the FlexSearch engine which indexes multiple tables (Name, Brand, Category) to return rapid, highly relevant results without heavy `LIKE %...%` queries.
  - **Variant Selection:** On the product details page, selecting different variants dynamically updates the displayed price and available stock using JavaScript/AJAX.

### 3.5 Wishlist System
- **What:** A persistent feature allowing logged-in users to save products for later.
- **How it Works:**
  - Authenticated users click a heart icon, triggering an AJAX POST request to `WishlistController`.
  - The `WishlistService` checks if the item is already saved; if not, it attaches the `product_id` to the `user_id` in the `wishlists` table.
- **Implementation Details:** 
  - The wishlist view dynamically calculates the "Net Price" of items, automatically resolving whether the product should display its base price or its lowest available variant price.

### 3.6 Site Settings & Dynamic Configuration
- **What:** Admin-controlled global settings for SEO, Branding, SMTP, and Contact Information.
- **How it Works:** 
  - **General Settings:** Stores business name, logos (dark/light), favicons, and currency in the database.
  - **Mail Settings:** Stores SMTP credentials.
  - **Contact Settings:** Stores company name, email, phone number, physical address, a Google Maps integration link (`map_link`), and dynamic social media URLs (Facebook, Instagram, TikTok, X, Threads, LinkedIn, WhatsApp, YouTube) with visibility toggles.
- **Implementation Details:** 
  - **Dynamic Boot Overrides:** In `AppServiceProvider::boot()`, the system queries the `general_settings` and `mail_settings` tables. If records exist, it dynamically overrides Laravel's config (`config(['app.name' => $gs->business_name])` and `config(['mail.mailers.smtp...'])`). This allows the admin to change email servers and site names without touching `.env` files.
  - **Contact & Social Data Integration:** The `ContactSetting` model is accessible globally via `App\HelperClass::contactSettings()`. This data is dynamically injected into printable templates (Invoices), the client-side **Contact Page**, the **Footer**, and both **Desktop/Mobile Headers**. Icons for social media only render if their specific status is toggled "On" in the Admin Panel and a URL is provided.
  - **Homepage Sections:** `SectionSetting` records control the visibility (True/False) and logic (e.g., Organic bestsellers vs Custom selected) of homepage UI blocks. 
    - **Dynamic Backgrounds:** The "Featured" section on the homepage dynamically loads its background image from the `background_image` field in `SectionSetting`. This is applied as an inline CSS style to the section container, allowing admins to fully customize the section's visual theme from the Admin Panel.

### 3.7 Hybrid Shopping Cart System
- **What:** A persistent shopping cart that works for both guests (visitors) and authenticated users.
- **How it Works:**
  - **Guest Users:** Cart items are stored in the Laravel `Session`.
  - **Authenticated Users:** Cart items are stored in the `carts` database table.
  - **Synchronization:** When a guest logs in or registers, a listener/service automatically migrates their session cart data into the database, ensuring no items are lost.
- **Implementation Details:**
  - `CartService` acts as an abstraction layer. When `addToCart()` is called, the service checks `Auth::check()`. If true, it performs Eloquent inserts/updates; if false, it manipulates the session array.
  - **UI/UX:** Uses AJAX for adding items, updating quantities, and removing items. The frontend dynamically updates the Mini-Cart, Cart Count, and Grand Totals without page reloads.

### 3.8 Checkout & Order Management System
- **What:** The complete flow from cart conversion to order fulfillment and admin tracking.
- **How it Works:**
  - **Shipping Methods:** Admins create shipping methods (Name, Price, Status). On the Cart page, customers select a method via AJAX, which temporarily stores the `shipping_method_id` in the session and updates the Grand Total.
  - **Checkout Processing:** The customer fills out their billing/shipping info. `OrderService::placeOrder()` retrieves the cart items, calculates final totals (including the selected shipping charge), and inserts a record into `orders` and multiple records into `order_items`.
  - **Order ID Generation:** A unique tracking ID (e.g., `ORD-XXXXXXXXXX`) is generated programmatically to ensure collision-free tracking.
  - **Cart Clearing:** Upon successful insertion, the `CartService` clears the session or database cart.
- **Implementation Details:** 
  - Supports Cash on Delivery (COD).
  - Triggers an `OrderConfirmationMail` to the customer immediately upon successful creation.

### 3.9 Order History & Guest Tracking
- **What:** Customer-facing interfaces to view past purchases and track order status.
- **How it Works:**
  - **Authenticated Users:** Can navigate to "My Orders" in their account dashboard. `OrderService::getUserOrders()` fetches paginated results explicitly tied to their `user_id`.
  - **Guest Tracking:** A public "Track Order" page allows anyone with a valid `order_id` to view the status. `OrderService::trackOrderById()` fetches the order and its items.
- **Implementation Details:**
  - **Visual Progress Bar:** The frontend Blade template uses a calculated index array `['Pending', 'Processing', 'Out for Delivery', 'Delivered']` to dynamically highlight a CSS progress bar based on the current `order_status`.

### 3.10 Invoice Management Module
- **What:** Automated and manual generation of printable/downloadable PDF-style invoices.
- **How it Works:**
  - **Admin Side:** Admins view an order and click "Generate Invoice". `OrderService::generateInvoice()` creates a sequential number (`INV-YYYYMMDD-0001`) and stamps the `invoice_date`. Admins can subsequently "Regenerate" (updates the date) or "View".
  - **Client Side:** Customers click "Download Invoice" on their order details page. If the invoice wasn't generated by the admin yet, the system auto-generates it on the fly to prevent errors.
- **Implementation Details (JS Print Engine):** 
  - To maximize performance and avoid heavy PHP PDF libraries (like DomPDF), the system uses a dedicated, highly-styled Blade view (`invoice-print.blade.php`).
  - This view utilizes `@media print` CSS to strip away all website UI (navbars, footers, buttons) and formats the tables perfectly for A4 printing.
  - A snippet of JavaScript (`window.onload = function() { window.print(); }`) automatically triggers the browser's native Print/Save-as-PDF dialog upon page load.

### 3.11 Customer Management Module (Admin)
- **What:** A comprehensive administrative interface to manage registered customers, their profiles, and their engagement history.
- **How it Works:** 
  - **Listing & Search:** Admins can view a paginated list of all authenticated users.
  - **Status Control:** Implements an "Active/Inactive" toggle. This is enforced at the database level via a `status` boolean in the `users` table. 
  - **Profile & History:** A dedicated details view fetches the user's profile data and eager-loads their entire purchase history (orders) for high-level customer analysis.
  - **Deletion:** Allows for permanent removal of customer accounts.
- **Implementation Details:** 
  - **`CustomerManagementService`:** Centralizes all logic for fetching users with their orders, toggling statuses, and deletion.
  - **Authentication Guardrail:** The `LoginRequest` is modified to include a `status => 1` check during `Auth::attempt()`. If a user is inactive, they are prevented from logging in with a specific error message.
  - **Admin UI:** Built with Bootstrap 5 switches for instant status toggling and clean detailed views for profile data.

### 3.12 Contact Message Management Module
- **What:** An automated system to handle customer inquiries submitted via the contact form.
- **How it Works:** 
  - **Form Submission:** Customers fill out a validated form (Name, Email, Subject, Message) on the Contact page. The form uses a standard synchronous POST submission.
  - **Validation Handling:** Server-side validation errors are displayed using Laravel's standard `@error` directives under each input field.
  - **Database Storage:** Submissions are stored in the `contact_messages` table for administrative tracking.
  - **Automated Feedback:** Upon submission, the system triggers a `ContactConfirmationMail` to the customer, providing an immediate professional acknowledgment. A Toastr success notification is displayed upon redirect.
  - **Admin Dashboard:** Admins can view a chronological list of messages, mark them as "Read," or delete them. New/Unread messages are visually highlighted (bold text).
  - **Message Detail View:** A dedicated view page allows admins to read the full content of a specific inquiry. Viewing a message automatically marks it as "Read" if it was previously unread.
- **Implementation Details:** 
  - **`ContactService`:** Orchestrates the storage of data and the mailing process. It includes a `try-catch` block for the mailer to ensure the user's experience isn't interrupted by SMTP connection issues. `getMessageById()` handles both data retrieval and the "Mark as Read" status update logic.
  - **Mailable:** Uses Laravel's Markdown mailables for a consistent, responsive email layout.
  - **Admin Controller:** `ContactMessageController` provides standard administrative actions (Index, Show, Read toggle, Delete) with SweetAlert2 protection for destructive actions.

### 3.13 Admin Profile Management
- **What:** Allows administrators to manage their own profile, including name, email, password, and a profile image.
- **How it Works:** 
  - Admins can edit their profile information through the Admin User Management module. 
  - The profile image is uploaded via a standard file input and stored on the server's public storage disk.
  - The authenticated admin's profile image is displayed in the admin panel header.
- **Implementation Details:** 
  - **AdminService:** Handles the logic for storing and updating admin records, including secure password hashing and image upload/deletion via `App\HelperClass`.
  - **File Storage:** Profile images are stored in `storage/app/public/upload/admins` and accessed via the `storage/` symbolic link.
  - **Global Header Integration:** The admin panel header dynamically retrieves the authenticated admin's image using the `admin` guard (`Auth::guard('admin')->user()->image`). If no image is set, it falls back to a default avatar.

---

## 4. Frontend & UI Standardization Refinements
- **Cart & Checkout UI:** Utilizes an 8/4 Bootstrap grid split. Flexbox `align-items-stretch` ensures promotional banners explicitly match the height of summary cards.
- **Mobile Header:** Implements a strict 3-6-3 column grid to ensure the hamburger menu, centered logo, and action icons (cart/user) remain perfectly aligned on small devices.
- **Search UI:** Streamlined to a pure text-input with FlexSearch autocomplete, omitting clunky category dropdowns for a cleaner aesthetic.
- **Navbar Simplification:** Removed redundant "Pages" and "Blog" menus from the main navigation. Replaced them with a consolidated "Account" dropdown menu that provides direct access to User Profile, Order History, Wishlist, and Authentication links (Login/Register/Logout) for both desktop and mobile views.
- **Button Standardization:** All action elements (Add to Cart, Track, Details, Start Shopping) strictly utilize core theme classes (e.g., Bootstrap `.btn` overrides combined with theme colors `#7AAACE` and `#333`, specific uppercase typography, and zero border-radius) ensuring 1:1 visual continuity.
- **Admin Assets:** Public admin CSS/JS directories renamed from `public/admin` to `public/admin_assets` to permanently resolve routing conflicts with the `Route::prefix('admin')` backend architecture.
- **Pagination UI Standardization:** All paginated index pages (both Admin and Client) are standardized to include "Showing X to Y of Z Results" text next to the pagination links. In the Admin panel, this is placed within a `card-footer` using a `d-flex justify-content-between` layout. On the Client side, it is centered above the pagination links to maintain visual balance.

---
*Note: This documentation is the source of truth for the smart-ecom project and is updated as the project evolves.*
