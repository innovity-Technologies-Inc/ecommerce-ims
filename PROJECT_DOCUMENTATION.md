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
- **What:** Separate login systems for Administrators and Customers, including modern social authentication options and bot protection.
- **How it Works:** 
  - **Multi-Guard Auth:** Implemented using Laravel Breeze with multiple authentication guards (`web` for customers, `admin` for administrators). 
  - **Social Login (Google):** Customers can log in or register instantly using their Google account via Laravel Socialite. Configuration is managed via `.env` (Client ID, Secret, Redirect URI).
  - **Google reCAPTCHA v2:** Enhanced security for Login and Registration pages via the "I'm not a robot" widget. Configuration is managed via `.env` (`NOCAPTCHA_SITEKEY`, `NOCAPTCHA_SECRET`).
- **Data & Storage:**
  - **Related Tables:** `users`, `admins`, `sessions`, `password_reset_tokens`
  - **Storage:** Data is stored in standard relational columns; social data (`google_id`, `google_token`) use `TEXT` for OAuth persistence.
  - **Fetching:** Managed by Laravel Breeze via `web` and `admin` guards; social data is retrieved via `Laravel\Socialite`; cart synchronization uses `CartService::syncCartOnLogin()`.
- **Implementation Details (Social Login):** 
  - **Social Login UI (REQ-68):** The traditional full-width "Google" text button has been replaced with a modern, multi-colored Google "G" SVG logo on both the **Login** and **Registration** pages. The design is borderless with a transparent background and features a smooth scaling hover effect.
  - **Registration Email Guardrail:** The `RegisteredUserController::store` method wraps the `Registered` event (which triggers email verification) in a `try-catch` block. If the system fails to send the email (e.g., SMTP connection issues), the user is still logged in but redirected with a Toastr `error` message: "We cant send email right now, Please try again.", instead of a Laravel debug error page.
  - **Database Integrity:**
    - The `password` field in the `users` table is nullable for social users.
    - `google_id` and `google_token` columns use the `TEXT` type to accommodate extremely long OAuth tokens provided by Google.
    - All profile fields (mobile, address, city, state, country, zip) are explicitly nullable to ensure smooth account creation from social providers.
  - **Security Guardrails:** 
    - **Inactivity Check:** The system explicitly checks the `status` of an existing user. If an account is deactivated (`status: 0`), the social login is blocked with the error: "Your account is inactive. Please contact support."
    - **Status Persistence:** For existing users, their current status is preserved during the login process, preventing unauthorized reactivation.
  - **Post-Login Workflow:**
    - **Cart Sync:** Guest items in the current session are automatically migrated to the user's account upon successful social login using `CartService::syncCartOnLogin()`.
    - **User Feedback:** Redirects to the home page with a standard "You are now logged in" success notification and `success` alert type.
  - **Account Profile UX:** The User Account Information page features persistent tab/collapse states. If a form submission fails (e.g., password validation error), the page automatically re-opens the relevant tab upon redirect. This is achieved by checking for specific field errors in the Blade view and utilizing a session-flashed `active_tab` variable.
  - **Product Form UX:** Mandatory fields in the Product Create/Edit form are clearly marked with a red asterisk (`*`). The image upload section includes explicit instructions regarding file size (max 600 KB per image) and allowed formats (JPEG, PNG, JPG, GIF, SVG, WEBP).
  - **Strict Validation:** `ProductRequest` enforces these UI constraints server-side, ensuring data integrity and preventing oversized media from impacting system performance.

### 3.2 Site Settings & Configuration
- **What:** Global settings for SEO, Branding, and Contact Information.
- **How it Works:** 
  - **General Settings:** Stores business name, logos (dark/light), favicons, and currency in the database.
  - **SMTP Mail & Social Login:** Credentials for these services are managed securely via the `.env` file to ensure system stability and security.
- **Data & Storage:**
  - **Related Tables:** `general_settings`, `section_settings`
  - **Storage:** Global settings and homepage section configurations are stored in dedicated single-row or configuration tables.
  - **Fetching:** Retrieved via `HelperClass::generalSettings()` and `SectionSetting` model; background images are loaded dynamically from the `background_image` column.
- **Implementation Details:** 
  - **Homepage Sections:** `SectionSetting` records control the visibility (True/False) and logic (e.g., Organic bestsellers vs Custom selected) of homepage UI blocks. 
    - **Dynamic Sections:** All homepage sections (Bestsellers, Hot Deals, Featured, Recently Added, Top Picks) can be independently enabled or disabled from the Admin Panel.
    - **Top Picks Section:** A curated section (Organic mode targets `is_new_arrival` products, or Custom mode) renamed from a redundant new arrivals section to provide more marketing flexibility.
    - **Dynamic Backgrounds:** The "Featured" section on the homepage dynamically loads its background image from the `background_image` field in `SectionSetting`.
 This is applied as an inline CSS style to the section container, allowing admins to fully customize the section's visual theme from the Admin Panel.

### 3.3 Catalog Management (Brands & Categories)
- **What:** Management of the foundational hierarchical data (Brands and Categories) that products belong to.
- **How it Works:** 
  - **Brands:** Managed via standard CRUD. They include a logo and a unique URL slug.
  - **Categories:** Implements a Parent/Child hierarchy allowing for sub-categories. 
- **Data & Storage:**
  - **Related Tables:** `brands`, `categories`
  - **Storage:** Stores names, slugs, logos (image paths), and parent-child hierarchy (for categories) in standard relational columns.
  - **Fetching:** Retrieved via `BrandService` and `CategoryService` using Eloquent; helper methods `HelperClass::getBrands()` and `HelperClass::getCategories()` are used for frontend display.
- **Implementation Details:** 
  - Slugs are automatically generated via Eloquent mutators or Service logic on creation.
  - Deleting a category safely handles child relationships (e.g., setting parent_id to null or cascading deletes, depending on schema constraints).

### 3.4 Product & Inventory System
- **What:** The core catalog engine supporting complex pricing, variants, and marketing flags.
- **How it Works:**
  - **Flexible Pricing Engine:** A product can have a `base_price` and multiple `ProductVariant` records. If variants exist, their specific pricing overrides the base price.
  - **Marketing Flags:** Boolean columns in the `products` table (e.g., `is_new`, `is_featured`, `is_hot_deal`) dictate where the product appears on the frontend.
  - **Multi-Image Gallery:** Handled via a dedicated `product_images` table, allowing infinite images per product with one designated as the primary thumbnail.
  - **Global Minimum Stock:** Each product can have a `min_stock_global` threshold. This is used to trigger "Low Stock" alerts on the dashboard if individual variant stock falls below this number.
- **Data & Storage:**
  - **Related Tables:** `products`, `product_variants`, `product_images`
  - **Storage:** Product metadata, marketing flags, base pricing, variant-specific prices/SKUs, and multiple image paths are stored across relational tables.
  - **Fetching:** Managed by `ProductService` using Eloquent with eager-loading (`with(['variants', 'images'])`); prices are resolved via `HelperClass::getProductPriceRange()`.
- **Implementation Details:** 
  - `ProductService` orchestrates the creation of the product, uploads the primary image, iterates through variant arrays to create `ProductVariant` rows (handling SKU and stock), and stores secondary images in the `product_images` table.
  - **Low Stock Thresholds:** System-wide default low stock limits have been removed. The system now relies exclusively on `min_stock_global` (at the Product level) and `min_stock_override` (at the Inventory Level). If a threshold is not set, it defaults to 0.

### 3.5 Customer Shop & Frontend Filtering
- **What:** The public-facing product catalog with advanced search, filtering, and sorting capabilities.
- **How it Works:**
  - **Sidebar Filtering:** Users can filter by Categories, Brands, and Price. The backend captures these query parameters (e.g., `?category=electronics&min_price=100`) and dynamically builds Eloquent queries in `FrontendController`/`ProductService`.
    - **Advanced Price Filtering:** The system implements a robust, variant-aware price filter. It prioritizes the `discount_price` if available; otherwise, it falls back to the `regular_price` (selling price). The filter explicitly checks both the base product and all associated variants. If a product's base price is `0` or `NULL` (indicating it is priced via variants), the product is still correctly included if any of its variants fall within the filtered range.
  - **Accurate Price Sorting:** The sorting logic ("Price: Low to High" and "Price: High to Low") utilizes an "effective price" calculation. To prevent inaccuracies caused by `0` or `NULL` base prices, the system uses a `DB::raw` `CASE` statement. It first checks for a valid base price (preferring discounts); if none exists, it scans all attached variants to find the lowest available price. This resulting `sort_price` guarantees consistent ordering across complex hybrid products.
  - **Global Search:** Powered by `FlexSearch`. When a user types in the navbar, the query is passed to the FlexSearch engine which indexes multiple tables (Name, Brand, Category) to return rapid, highly relevant results without heavy `LIKE %...%` queries.
  - **Variant Selection:** On the product details page, selecting different variants dynamically updates the displayed price and available stock using JavaScript/AJAX.
- **Data & Storage:**
  - **Related Tables:** `products`, `product_variants`, `brands`, `categories`
  - **Storage:** Relational data with query-driven parameters; search indices are managed by FlexSearch for rapid retrieval.
  - **Fetching:** `FrontendService` builds dynamic Eloquent queries based on request parameters; `FlexSearch` (via `daiyanmozumder/laravel-flexsearch`) handles high-performance global search.

### 3.6 Wishlist System
- **What:** A persistent feature allowing logged-in users to save products for later.
- **How it Works:**
  - Authenticated users click a heart icon, triggering an AJAX POST request to `WishlistController`.
  - $this->WishlistService checks if the item is already saved; if not, it attaches the `product_id` to the `user_id` in the `wishlists` table.
- **Data & Storage:**
  - **Related Tables:** `wishlists`, `products`
  - **Storage:** Links `user_id` and `product_id` in a simple pivot-style relational table.
  - **Fetching:** `WishlistService` retrieves saved items for the authenticated user; `HelperClass::wishlistCount()` provides real-time counts for the header.
- **Implementation Details:** 
  - The wishlist view dynamically calculates the "Net Price" of items, automatically resolving whether the product should display its base price or its lowest available variant price.

### 3.7 Site Settings & Dynamic Configuration
- **What:** Admin-controlled global settings for SEO, Branding, and Contact Information.
- **How it Works:** 
  - **General Settings:** Stores business name, logos (dark/light), favicons, and currency in the database.
  - **Contact Settings:** Stores company name, email, phone number, physical address, a Google Maps integration link (`map_link`), and dynamic social media URLs (Facebook, Instagram, TikTok, X, Threads, LinkedIn, WhatsApp, YouTube) with visibility toggles.
- **Data & Storage:**
  - **Related Tables:** `general_settings`, `contact_settings`
  - **Storage:** Site-wide metadata is stored in the database; critical service credentials (SMTP, OAuth) are stored in the `.env` file.
  - **Fetching:** Managed via `HelperClass::generalSettings()` and `HelperClass::contactSettings()` to ensure availability across all Blade templates without redundant queries.
- **Implementation Details:** 
  - **Contact & Social Data Integration:** The `ContactSetting` model is accessible globally via `App\HelperClass::contactSettings()`. This data is dynamically injected into printable templates (Invoices), the client-side **Contact Page**, the **Footer**, and both **Desktop/Mobile Headers**. Icons for social media only render if their specific status is toggled "On" in the Admin Panel and a URL is provided.
  - **App & SMTP Security:** System-critical credentials (SMTP, Social OAuth, reCAPTCHA) are strictly managed via the `.env` file to prevent unauthorized administrative changes and ensure development-to-production environment integrity.

### 3.8 Hybrid Shopping Cart System
- **What:** A persistent shopping cart that works for both guests (visitors) and authenticated users.
- **How it Works:**
  - **Guest Users:** Cart items are stored in the Laravel `Session`.
  - **Authenticated Users:** Cart items are stored in the `carts` database table.
  - **Synchronization:** When a guest logs in or registers, a listener/service automatically migrates their session cart data into the database, ensuring no items are lost.
  - **Optimized Variant Display:** Variant details in the cart, mini-cart, and checkout are intelligently formatted. The system checks for available size and color attributes; if missing, it falls back to the `variant_name` to prevent broken UI elements (like empty slashes).
- **Data & Storage:**
  - **Related Tables:** `carts`, `products`, `product_variants`
  - **Storage:** Authenticated cart data is persistent in the database; guest data is volatile within the Laravel `session` driver.
  - **Fetching:** `CartService` acts as an abstraction layer, resolving `Auth::check()` to switch between database and session data sources seamlessly.
- **Implementation Details:**
  - `CartService` acts as an abstraction layer. When `addToCart()` is called, the service checks `Auth::check()`. If true, it performs Eloquent inserts/updates; if false, it manipulates the session array.
  - **UI/UX:** Uses AJAX for adding items, updating quantities, and removing items. The frontend dynamically updates the Mini-Cart, Cart Count, and Grand Totals without page reloads.

### 3.9 Checkout & Order Management System
- **What:** The complete flow from cart conversion to order fulfillment and admin tracking.
- **How it Works:**
  - **Shipping Methods:** Admins create shipping methods (Name, Price, Status). On the Cart page, customers select a method via AJAX, which temporarily stores the `shipping_method_id` in the session and updates the Grand Total.
  - **Checkout Processing:** The customer fills out their billing/shipping info. `OrderService::placeOrder()` retrieves the cart items, calculates final totals (including the selected shipping charge), and inserts a record into `orders` and multiple records into `order_items`.
  - **Order ID Generation:** A unique tracking ID (e.g., `ORD-XXXXXXXXXX`) is generated programmatically to ensure collision-free tracking.
  - **Cart Clearing:** Upon successful insertion, the `CartService` clears the session or database cart.
- **Data & Storage:**
  - **Related Tables:** `orders`, `order_items`, `shipping_methods`, `carts`
  - **Storage:** Relational storage for order headers and granular line items; transient shipping selections are stored in the `session`.
  - **Fetching:** `OrderService` handles complex transactional creation and retrieval using Eloquent with eager-loading for performance.
- **Implementation Details:** 
  - Supports Cash on Delivery (COD).
  - Triggers an `OrderConfirmationMail` to the customer immediately upon successful creation.

### 3.10 Order History & Guest Tracking
- **What:** Customer-facing interfaces to view past purchases and track order status.
- **How it Works:**
  - **Authenticated Users:** Can navigate to "My Orders" in their account dashboard. `OrderService::getUserOrders()` fetches paginated results explicitly tied to their `user_id`.
  - **Guest Tracking:** A public "Track Order" page allows anyone with a valid `order_id` to view the status. `OrderService::trackOrderById()` fetches the order and its items.
- **Data & Storage:**
  - **Related Tables:** `orders`, `order_items`, `order_status_logs`
  - **Storage:** Historical order data and status transitions are stored in relational columns.
  - **Fetching:** `OrderService` provides paginated account history for logged-in users and ID-based lookups for guest tracking.
- **Implementation Details:**
  - **Visual Progress Bar:** The frontend Blade template uses a calculated index array `['Pending', 'Processing', 'Out for Delivery', 'Delivered']` to dynamically highlight a CSS progress bar based on the current `order_status`.

### 3.11 Invoice Management Module
- **What:** Automated and manual generation of printable/downloadable PDF-style invoices.
- **How it Works:**
  - **Admin Side:** Admins view an order and click "Generate Invoice". `OrderService::generateInvoice()` creates a sequential number (`INV-YYYYMMDD-0001`) and stamps the `invoice_date`. Admins can subsequently "Regenerate" (updates the date) or "View".
  - **Client Side:** Customers click "Download Invoice" on their order details page. If the invoice wasn't generated by the admin yet, the system auto-generates it on the fly to prevent errors.
- **Data & Storage:**
  - **Related Tables:** `orders`, `order_items`
  - **Storage:** Sequential invoice IDs (`invoice_no`) and generation dates are stored directly within the `orders` table.
  - **Fetching:** `OrderService::generateInvoice()` handles the assignment; the printable view retrieves order details via Eloquent for real-time rendering.
- **Implementation Details (JS Print Engine):** 
  - To maximize performance and avoid heavy PHP PDF libraries (like DomPDF), the system uses a dedicated, highly-styled Blade view (`invoice-print.blade.php`).
  - This view utilizes `@media print` CSS to strip away all website UI (navbars, footers, buttons) and formats the tables perfectly for A4 printing.
  - A snippet of JavaScript (`window.onload = function() { window.print(); }`) automatically triggers the browser's native Print/Save-as-PDF dialog upon page load.

### 3.12 Customer Management Module (Admin)
- **What:** A comprehensive administrative interface to manage registered customers, their profiles, and their engagement history.
- **How it Works:** 
  - **Listing & Search:** Admins can view a paginated list of all authenticated users.
  - **Status Control:** Implements an "Active/Inactive" toggle. This is enforced at the database level via a `status` boolean in the `users` table. 
  - **Profile & History:** A dedicated details view fetches the user's profile data and eager-loads their entire purchase history (orders) for high-level customer analysis.
  - **Deletion:** Allows for permanent removal of customer accounts.
- **Data & Storage:**
  - **Related Tables:** `users`, `orders`
  - **Storage:** Standard customer profiles with a `status` boolean for access control.
  - **Fetching:** `CustomerManagementService` fetches paginated `User` records with a `withCount('orders')` aggregate for high-level listing.
- **Implementation Details:** 
  - **`CustomerManagementService`:** Centralizes all logic for fetching users with their orders, toggling statuses, and deletion.
  - **Authentication Guardrail:** The `LoginRequest` is modified to include a `status => 1` check during `Auth::attempt()`. If a user is inactive, they are prevented from logging in with a specific error message.
  - **Admin UI:** Built with Bootstrap 5 switches for instant status toggling and clean detailed views for profile data.

### 3.13 Contact Message Management Module
- **What:** An automated system to handle customer inquiries submitted via the contact form.
- **How it Works:** 
  - **Form Submission:** Customers fill out a validated form (Name, Email, Subject, Message) on the Contact page. The form uses a standard synchronous POST submission.
  - **Validation Handling:** Server-side validation errors are displayed using Laravel's standard `@error` directives under each input field.
  - **Database Storage:** Submissions are stored in the `contact_messages` table for administrative tracking.
  - **Automated Feedback:** Upon submission, the system triggers a `ContactConfirmationMail` to the customer, providing an immediate professional acknowledgment. A Toastr success notification is displayed upon redirect.
  - **Admin Dashboard:** Admins can view a chronological list of messages, mark them as "Read," or delete them. New/Unread messages are visually highlighted (bold text).
  - **Message Detail View:** A dedicated view page allows admins to read the full content of a specific inquiry. Viewing a message automatically marks it as "Read" if it was previously unread.
- **Data & Storage:**
  - **Related Tables:** `contact_messages`
  - **Storage:** Stores submission metadata (Name, Email, Subject, Message) and an `is_read` boolean for workflow tracking.
  - **Fetching:** `ContactService` manages message retrieval and the "Mark as Read" status update; mail notifications use standard Laravel SMTP configuration.
- **Implementation Details:** 
  - **`ContactService`:** Orchestrates the storage of data and the mailing process. It includes a `try-catch` block for the mailer to ensure the user's experience isn't interrupted by SMTP connection issues. `getMessageById()` handles both data retrieval and the "Mark as Read" status update logic.
  - **Mailable:** Uses Laravel's Markdown mailables for a consistent, responsive email layout.
  - **Admin Controller:** `ContactMessageController` provides standard administrative actions (Index, Show, Read toggle, Delete) with SweetAlert2 protection for destructive actions.

### 3.14 Admin Profile Management
- **What:** Allows administrators to manage their own profile, including name, email, password, and a profile image.
- **How it Works:** 
  - Admins can edit their profile information through the Admin User Management module. 
  - The profile image is uploaded via a standard file input and stored on the server's public storage disk.
  - The authenticated admin's profile image is displayed in the admin panel header.
- **Data & Storage:**
  - **Related Tables:** `admins`
  - **Storage:** Admin credentials and profile image paths are stored in standard relational columns.
  - **Fetching:** Managed by `AdminService`; the authenticated admin's data is retrieved via `Auth::guard('admin')->user()`.
- **Implementation Details:** 
  - **AdminService:** Handles the logic for storing and updating admin records, including secure password hashing and image upload/deletion via `App\HelperClass`.
  - **File Storage:** Profile images are stored in `storage/app/public/upload/admins` and accessed via the `storage/` symbolic link.
  - **Global Header Integration:** The admin panel header dynamically retrieves the authenticated admin's image using the `admin` guard (`Auth::guard('admin')->user()->image`). If no image is set, it falls back to a default avatar.

### 3.15 Product Status Management (Active/Discontinued)
- **What:** Allows administrators to toggle a product's availability status (Active or Discontinued) without deleting the product from the database.
- **How it Works:** 
  - Admins can toggle the status directly from the Product List index page using a quick-action switch, or explicitly set it when creating/editing a product.
  - Discontinued products remain in the catalog but prominently display a red "Discontinued" badge.
  - The "Add to Cart" functionality is disabled on both the product list cards and the product details page for discontinued items.
- **Data & Storage:**
  - **Related Tables:** `products`
  - **Storage:** Managed via a single `status` boolean column in the `products` table.
  - **Fetching:** Eloquent queries in `ProductService` handle the status updates; Blade views conditionally render badges and buttons based on this boolean.
- **Implementation Details:** 
  - **Database:** A `status` boolean column defaults to `true` on the `products` table.
  - **ProductService:** Includes a `toggleStatus()` method to handle the fast AJAX/form toggles from the admin index.
  - **Client UI:** Blade directives (`@if(!$product->status)`) conditionally render the "Discontinued" warning badge and replace the standard "Add to Cart" button with a disabled "Product Unavailable" button, or swap it for a "View Details" link to prevent cart entries.

### 3.16 Admin Live Search, Filter, and Sort
- **What:** A high-performance, AJAX-driven system for real-time searching, filtering, and sorting across all administrative index pages.
- **How it Works:** 
  - **Live Search:** As an admin types in the search box, the system waits for a short debounce period (500ms) before triggering an AJAX request to fetch filtered results.
  - **Dynamic Sorting:** Admins can sort data by "Latest", "Oldest", "A to Z", and "Z to A" via a dropdown menu, which updates the list instantly without page reloads.
  - **Advanced Order Filtering:** Specifically for the Orders module, admins can filter results by Order Status, Payment Method, Payment Status, and a custom Date Range (From/To).
  - **Seamless Pagination:** Pagination links are intercepted via jQuery to ensure that active search and sort parameters are preserved during navigation.
  - **URL Synchronization:** The browser's URL is dynamically updated using `window.history.pushState` to reflect the current search/sort state, allowing for easy bookmarking and sharing.
- **Data & Storage:**
  - **Related Tables:** `products`, `orders`, `users`, `brands`, `categories`, `inventory_levels` (varies by module)
  - **Storage:** Relational data stored in standard columns; search indices are maintained by `FlexSearch`.
  - **Fetching:** `FlexSearch` (via Service Layer) performs rapid multi-column lookups; controllers return AJAX-friendly partial views (`partials/table`) for surgical UI updates.
- **Implementation Details:** 
  - **FlexSearch Engine:** Integrated `laravel-flexsearch` in the Service layer to handle complex, multi-column search queries efficiently.
  - **Architecture:** Each module (Products, Orders, Customers, etc.) follows a consistent pattern:
    - **Service:** Accepts a `$params` array to apply search, sort, and specific filters (like status or date ranges) to the Eloquent query.
    - **Controller:** Detects AJAX requests and returns a specialized partial view containing only the table and pagination.
    - **Views:** Uses a main `index` view for the layout and a `partials/table` view for the data rows, enabling surgical UI updates.
  - **Frontend:** A centralized jQuery script manages the AJAX calls, loading states (opacity feedback), and error handling.

### 3.17 Admin Product Management Module
- **What:** A central hub for administrators to manage the entire product catalog, including inventory, pricing, and hierarchical categorization.
- **How it Works:** 
  - **Enhanced Listing:** Displays a comprehensive table of products with primary images, dynamic pricing ranges, and status toggles.
  - **Hierarchical Display:** The "Category" column dynamically renders both the parent category and its assigned subcategory (e.g., "Electronics > Smartphones") using a nested visual indicator.
  - **Advanced Filtering Layout:** Implements a logical two-row filter bar for high efficiency:
    - **Row 1:** High-level search and hierarchical categorization (Category & Subcategory).
    - **Row 2:** Brand, Status, Sorting, and a dedicated Reset button for rapid clearing.
  - **Dynamic Dependencies:** The filtering UI includes a reactive Subcategory dropdown that automatically populates via jQuery based on the selected parent Category, maintaining state persistence across AJAX calls.
  - **Quick Actions:** Allows for instant status updates (Active/Discontinued) via AJAX-enabled switches directly from the index.
- **Data & Storage:**
  - **Related Tables:** `products`, `product_variants`, `product_images`, `categories`, `brands`
  - **Storage:** Comprehensive relational data including metadata, pricing, stock levels, and multiple image paths.
  - **Fetching:** `ProductService::getAllProducts()` utilizes heavy eager-loading (`with(['primaryImage', 'category', 'subCategory', 'brand'])`) to minimize database queries.
- **Implementation Details:** 
  - **Optimized Data Retrieval:** `ProductService::getAllProducts()` utilizes eager-loading (`with(['primaryImage', 'category', 'subCategory', 'brand'])`) to ensure maximum performance and minimal database overhead.
  - **Filter Logic:** Integrated `laravel-flexsearch` to handle multi-column filtering parameters seamlessly within the service layer.
  - **Frontend Reactivity:** Uses a specialized jQuery handler to manage the dynamic population of subcategories, URL synchronization via `history.pushState`, and debounced AJAX-based list updates.
  - **Visual Hierarchy:** Uses Bootstrap icons and typography (`<small>`, `bx-subdirectory-right`) to clearly differentiate between parent and child categories in the admin UI.

### 3.18 Coupon Management Module
- **What:** A comprehensive promotion engine allowing administrators to create and manage discount coupons, and customers to apply them at checkout.
- **How it Works:** 
  - **Flexible Application:** Coupons can be applied to either the "Total Product Price" or the "Shipping Cost".
  - **Discount Logic:** Supports both "Percentage" and "Fixed" discount types. For percentage discounts, an optional "Maximum Discount Amount" can be set to cap the total savings.
  - **Usage Controls:** Includes "Minimum Spend" requirements and "Usage Limits" (total times a coupon can be used).
  - **Client-Side Application:** Customers can apply coupons on the Checkout page. The system uses AJAX to validate the code and instantly update the Discount and Grand Total in the order summary without a page reload.
  - **Usage Tracking & Audit:** Every successful coupon application is recorded in the `coupon_usages` table. Administrators can access a dedicated **Usage History** page for any specific coupon via a history icon in the coupon list. This page provides a granular audit trail including Customer Name, Email, Order ID, exact discount applied, and timestamp.
  - **Advanced Admin Filtering:** Admins can search by code and filter by Application Area (Product vs Shipping), Status (Active/Inactive), and two separate date ranges (Active Range and Expiry Range).
- **Data & Storage:**
  - **Related Tables:** `coupons`, `coupon_usages`, `orders`
  - **Storage:** Discount logic, usage constraints, and a permanent history of applications are stored across relational tables.
  - **Fetching:** `CouponService` handles validation and usage recording; `session('coupon')` persists the active discount during the checkout flow.
- **Implementation Details:** 
  - **Service Layer Pattern:** `CouponService` centralizes all CRUD logic, multi-column filtering, validation, discount calculation, and history retrieval.
  - **Model Validation:** The `Coupon` model includes an `isValid()` method that encapsulates the complex business logic for checking status, date ranges, and usage limits in a single, reusable call.
  - **Checkout Integration:** `OrderService::placeOrder()` performs a final server-side validation of the coupon before creating the order. It then calls `CouponService::recordUsage()` to update the tracking history and increment the global `used_count`.
  - **Session Management:** Applied coupons are temporarily stored in the session (`session('coupon')`) during the checkout process to ensure persistence and easy removal.
  - **Frontend Interactivity:** Uses jQuery for dynamic form field visibility in the Admin panel and AJAX-based application/removal with Toastr notifications on the Client-side checkout page.

### 3.19 Flash Sale Module
- **What:** A time-sensitive promotional system that applies deep discounts to a selected group of products without overwriting their standard discounts.
- **How it Works:**
  - **Centralized Control:** Managed via a single administrative form that controls the global Flash Sale status (Active/Inactive) and its end date.
  - **Dynamic Product Selection:** Admins can search, filter, and add products from the entire active catalog using a high-performance AJAX selector.
  - **Dedicated Pricing Logic:** Flash Sale discounts are stored in dedicated `flash_discount_price` and `flash_discount_percentage` fields in both `products` and `product_variants` tables. This ensures that a product's standard `discount_price` remains untouched and is restored once the Flash Sale ends.
  - **Automated Price Sync:** When a Flash Sale is activated, the system automatically calculates and updates the `flash_discount_*` fields.
  - **Homepage Section & Timer:** A dedicated Flash Sale section appears on the homepage only when a sale is active and hasn't expired. It includes a real-time JavaScript countdown timer and a "View All" button.
  - **Automatic Expiry (Automation):** The system automatically detects when a Flash Sale's `end_date` has passed and resets all associated `flash_discount_*` fields to 0, causing the system to fall back to standard discount or regular prices.
- **Data & Storage:**
  - **Related Tables:** `flash_sales`, `products`, `product_variants`
  - **Storage:** Flash sale metadata and temporary price overrides are stored directly in the product and variant tables to ensure high-performance frontend rendering.
  - **Fetching:** `FlashSaleService` manages the lifecycle; automated resets are handled by an Artisan command triggered via the system scheduler.
- **Implementation Details:**
  - **`isActive()` Helper:** The `FlashSale` model includes an `isActive()` method that checks both the `status` toggle and the `end_date` against the current time.
  - **`HelperClass::getProductPriceRange()`:** This central pricing logic prioritizes `flash_discount_price` if `is_flash_sale` is true; otherwise, it falls back to the standard discount or regular price.
  - **Console Command:** A custom Artisan command `flash-sale:check-expiry` is registered in `routes/console.php`. This command triggers the `FlashSaleService::syncAllDiscounts()` method.
  - **Scheduler:** The command is scheduled to run `everyMinute()`, ensuring prices revert to normal as soon as the sale ends.

### 3.20 Server Automation (Cron Jobs)
- **What:** Required configuration to enable automated tasks like Flash Sale expiry and order cleanups.
- **How to Set Up (cPanel):**
  
  **Method A: CLI Method (Recommended)**
  1. Log in to cPanel and search for **Cron Jobs**.
  2. Under "Add New Cron Job", select **Once Per Minute** (`* * * * *`).
  3. Enter the following command:
     `php /home/username/public_html/artisan schedule:run >> /dev/null 2>&1`
  4. Click **Add New Cron Job**.

  **Method B: Web Route Method (Alternative)**
  If your hosting doesn't allow CLI execution, you can use a web-based cron service (like EasyCron) or a cPanel "GET" request to trigger the following URL every minute. This route specifically executes the Flash Sale expiry check logic:
  `https://smart-ecom.com/check-flash-sale-expiry`
  
  *In cPanel Cron Jobs:*
  `curl -s https://smart-ecom.com/check-flash-sale-expiry > /dev/null 2>&1`
- **Data & Storage:**
  - **Related Tables:** `flash_sales`, `products`, `product_variants`
  - **Storage:** Automated processes update boolean flags and price overrides in the database based on temporal conditions.
  - **Fetching:** Triggered by the Laravel Scheduler (`artisan schedule:run`) which calls specific Service Layer methods (`syncAllDiscounts`).
- **Note:** This automation ensures that time-sensitive features like Flash Sale discounts are reset precisely when they expire.

### 3.21 Frontend Refactoring & Public Invoice
- **What:** Architectural cleanup of the Frontend module and expansion of order tracking features.
- **How it Works:**
  - **Thin Controllers:** `FrontendController` was refactored to remove all business logic, moving it to `FrontendService`.
  - **Service Layer Pattern:** `FrontendService` now centralizes all product filtering (Category, Brand, Price, Search), related products logic, and sorting.
  - **Form Requests:** `ProductFilterRequest` and `TrackOrderRequest` handle all input validation, ensuring clean and secure data flow.
  - **Public Invoice Access:** Customers can now print invoices directly from the Order Tracking results without needing an account. This is enabled by a `publicInvoice` route that automatically generates an invoice if it doesn't already exist.
- **Data & Storage:**
  - **Related Tables:** `orders`, `order_items`, `products`, `product_variants`
  - **Storage:** Standard relational order data.
  - **Fetching:** `FrontendService` centralizes all product query logic; the `publicInvoice` route retrieves order details via a unique Order ID to provide unauthenticated (but secure) print access.
- **Implementation Details:**
  - **Refined Price Filter:** The pricing logic in `FrontendService` prioritizes `discount_price` and correctly handles products whose price is defined solely in variants (0/NULL base price) by performing a nested variant check.
  - **Security:** While public, the invoice access is protected by the unique Order ID requirement and is read-only.

### 3.21 Product Stock Display
- **What:** Real-time visibility of inventory levels for both admins and customers.
- **How it Works:**
  - **Admin Interface:** The product details page in the admin panel now displays "Base Stock" for simple products and a detailed breakdown of stock for each variant in the variation table.
  - **Client Interface:** The product details page features an "Availability" badge that dynamically updates based on the selected variant.
  - **Dynamic Interactivity:** Integrated with JavaScript, selecting a variant instantly updates the stock status (e.g., "15 In Stock" vs "Out of Stock").
- **Data & Storage:**
  - **Related Tables:** `products`, `product_variants`
  - **Storage:** Inventory levels are stored in the `stock` column of both the base `products` and the specific `product_variants` tables.
  - **Fetching:** Real-time lookups are performed via AJAX when a variant is selected on the storefront; the admin panel retrieves data directly via Eloquent relationships.
- **Implementation Details:**
  - **Unified Stock Tracking:** Supports the hybrid inventory model where stock can be managed globally at the product level (Base Stock) or specifically per variant.
  - **Auto-Disable Cart:** The "Add to Cart" button is automatically hidden if the selected item/variant is out of stock, ensuring a smooth customer experience.

### 3.22 Automated Stock Management
- **What:** Automatic synchronization of inventory levels during the order lifecycle.
- **How it Works:**
  - **Deduction on Placement:** When a customer places an order (`placeOrder`), the system automatically decrements the stock for each purchased item (either base stock or variant stock) based on the quantity ordered.
  - **Restoration on Cancellation/Rejection:** If an admin or system update changes an order's status to `Cancelled` or `Rejected`, the system automatically increments the stock back to its previous levels.
  - **Status Reversibility:** If an order is moved from a restorative status (`Cancelled`/`Rejected`) back to an active status (e.g., `Pending`), the stock is intelligently re-deducted.
- **Data & Storage:**
  - **Related Tables:** `products`, `product_variants`, `orders`, `order_items`
  - **Storage:** Numeric stock values are updated atomically in the database.
  - **Fetching:** Triggered internally by `OrderService` within database transactions to ensure accuracy and prevent race conditions.
- **Implementation Details:**
  - **Transaction Integrity:** All stock adjustments are wrapped in database transactions (`DB::transaction`) within the `OrderService` to prevent data inconsistencies.
  - **Intelligent Logic:** The `adjustStock` helper method in `OrderService` handles the heavy lifting, ensuring that variant-specific stock is prioritized over base stock where applicable.

### 3.23 Order Status Finality & Flow Refinement
- **What:** Enforces a terminal state for orders and streamlines the administrative status workflow.
- **How it Works:**
  - **Restriction:** Once an order's status is set to `Delivered`, `Cancelled`, or `Rejected`, it becomes final. No further status transitions are allowed.
  - **Workflow Optimization:** The `Pending` status has been removed from the manual update dropdown. Orders start as `Pending` by default upon creation, and admins can move them forward to `Processing`, `Out for Delivery`, etc., but cannot manually revert an order back to a `Pending` state.
  - **Process Integrity:** To prevent cyclical workflows and redundant logging, the system prevents an order from being moved to any status it has previously held. For example, if an order moves from `Processing` to `Out for Delivery`, it cannot be moved back to `Processing`.
  - **UI Feedback:** The status update form in the Admin Order Details page is automatically hidden and replaced with an informative alert message (Success for Delivered, Danger for Cancelled/Rejected) when an order reaches a terminal state.
- **Data & Storage:**
  - **Related Tables:** `orders`, `order_status_logs`
  - **Storage:** Terminal statuses are enforced via logical checks against the current `order_status` column and the `order_status_logs` history.
  - **Fetching:** `OrderService` validates the feasibility of a status change by eager-loading the `statusLogs` relationship.
- **Implementation Details:**
  - **Service-Level Guard:** `OrderService::updateOrderStatus` throws an exception if an attempt is made to change the status of an already delivered, cancelled, or rejected order, or if the new status exists in the order's history (`statusLogs`).
  - **Controller Handling:** `OrderController` catches these exceptions and returns a session error message to the admin.

### 3.24 Admin Advanced Dashboard
- **What:** A comprehensive, real-time analytics hub for administrators to monitor business performance and inventory health.
- **How it Works:**
  - **Performance Metrics:** Displays key financial indicators including:
    - **Total Sales Review:** Monthly and yearly sales totals, along with the lifetime cumulative sales amount.
    - **Inventory Overview:** Real-time counts of total products and low-stock alerts.
    - **Customer & Order Analytics:** Tracks total registered customers, this month's order volume, and pending (unfulfilled) orders.
  - **Visual Data Representation:** Features an interactive **Sales Review Chart** (ApexCharts) that plots monthly sales data for the current year, enabling rapid identification of seasonal trends.
  - **Proactive Inventory Management:** Automatically identifies and lists products with variants that have fallen below their specific thresholds (`min_stock_global` for products or `min_stock_override` for warehouse levels).
  - **Business Intelligence:** Displays a "Best Selling Products" leaderboard based on the lifetime `sales_count` attribute, highlighting top-performing inventory.
- **Data & Storage:**
  - **Related Tables:** `orders`, `order_items`, `users`, `products`, `inventory_levels`, `warehouse_stock_limits`
  - **Storage:** Aggregated business intelligence data derived from the entire relational database.
  - **Fetching:** `DashboardService` executes optimized SQL aggregates (`SUM`, `COUNT`) and passes formatted JSON data to ApexCharts for visual rendering.
- **Implementation Details:**
  - **`DashboardService`:** Centralizes all complex aggregation logic. It utilizes optimized Eloquent queries with `SUM()` and `COUNT()` aggregates to ensure high performance even as the database grows.
  - **Dynamic Charting:** Monthly sales data is retrieved using `DB::raw` with `MONTH()` groupings and passed to an area-style ApexChart via JSON encoding in the Blade view.
  - **Customizable Alerts:** Low stock thresholds are managed per-product and per-inventory level, allowing for granular control over what constitutes a stock emergency for different items.
  - **Integrated Navigation:** The dashboard provides direct links to "Restock" low-stock items, filtered order lists, and a dedicated **Best Selling Products** report.

### 3.25 Best Selling Products Report (Admin)
- **What:** A specialized reporting page that identifies top-performing products over different time periods.
- **How it Works:**
  - **Time-Based Filtering:** Admins can filter the best-selling list by "Monthly" (current month), "Yearly" (current year), or "All Time".
  - **Delivered Orders Only:** To ensure accuracy, the sales count is calculated by summing quantities from `order_items` that belong to orders with a `Delivered` status.
  - **Live Dashboard Integration:** The main dashboard features two quick-glance cards for Monthly and Yearly best sellers, each with a "View All" button that redirects to the full paginated report with the corresponding filter pre-applied.
- **Data & Storage:**
  - **Related Tables:** `products`, `order_items`, `orders`
  - **Storage:** Aggregated quantity totals per product/variant.
  - **Fetching:** `DashboardService::getBestSellingProductsPaged()` joins `products` with `order_items` (filtering for 'Delivered' orders) to compute precise sales volumes.
- **Implementation Details:**
  - **`DashboardService::getBestSellingProductsPaged()`:** Centralizes the logic using optimized joins and `SUM()` aggregates.
  - **AJAX-Driven:** The report supports instant filtering and pagination without full page reloads, maintaining URL state via `history.pushState`.

### 3.26 Global Wishlist Logic
- **What:** Centralized implementation of wishlist functionality to ensure consistent behavior across all storefront pages.
- **How it Works:** 
  - **Global Form & Function:** The hidden form and `addToWishlist` JavaScript function are placed within `master.blade.php`, making them available on the homepage, shop page, and product details pages.
  - **Functional Cart Integration:** The wishlist page includes a context-aware "Add to Cart" button that intelligently handles simple products, variant products, and discontinued items.
- **Data & Storage:**
  - **Related Tables:** `wishlists`
  - **Storage:** Relational mapping between `user_id` and `product_id`.
  - **Fetching:** `WishlistService` provides backend verification; the wishlist page eager-loads product metadata to determine the appropriate action (Add to Cart vs View Details).
- **Implementation Details:**
  - **JS Orchestration:** `addToWishlist(productId)` dynamically populates a hidden input and submits the form to the `user.wishlist.store` route.
  - **Smart Actions:** The wishlist table's action button dynamically switches between "Add to Cart" (AJAX-powered), "Select Options" (Redirects to details), and "View Details" based on the product's live status and configuration.

### 3.26 Bulk Product Upload Module
- **What:** A high-performance administrative tool to import products and their variants in bulk from Excel or CSV files.
- **How it Works:**
  - **Template-Based Import:** Admins can download both CSV and XLSX pre-formatted templates to ensure data compatibility.
  - **Hierarchical Resolution:** The system automatically maps Category, Subcategory, and Brand names from the file to their corresponding database IDs.
  - **Variant Support:** Supports complex product structures. If multiple rows share the same Product Name, the system treats the first row as the product definition and subsequent rows as variants, grouping them logically.
  - **Auto-Calculation:** Automatically calculates the `discount_price` based on the provided `regular_price` and `discount_percentage` for both products and variants.
  - **Validation:** Implements file-level validation (format, size) and row-level validation (required fields, numeric types). It also includes fallback support for common MIME types on different operating systems.
- **Data & Storage:**
  - **Related Tables:** `products`, `product_variants`, `product_images`, `categories`, `brands`
  - **Storage:** Massive batch insertion/update of relational data.
  - **Fetching:** `ProductsImport` (via `maatwebsite/excel`) reads files into collections and uses stateful logic to resolve name-to-ID mappings before persistence.
- **Implementation Details:**
  - **Laravel Excel (`maatwebsite/excel`):** Utilizes `ToCollection` and `WithHeadingRow` for efficient dataset handling.
  - **Atomic Transactions:** The `importProducts` method is wrapped in a `DB::transaction` to ensure all-or-nothing data integrity during the import process.
  - **`ProductsImport` Service:** Uses a stateful `lastProductName` tracker to identify when to update a product versus when to append a variant, preventing redundant database calls.
  - **Slug & SKU Generation:** Automatically generates unique URL slugs for products and unique SKUs (with a random suffix) for variants during the import process.
  - **Test Data:** A dedicated `test_data/` directory is maintained with pre-filled CSV and XLSX files for rapid verification of import logic.

### 3.27 Return Module
- **What:** A comprehensive return management system allowing both guests and authenticated users to request returns for delivered items, with a full administrative workflow for approval, receiving, and stock/sales adjustment.
- **How it Works:**
  - **Guest Returns:** Unauthenticated users can access a dedicated "/returns" page, enter their Order ID, and fetch order details via AJAX. **Validation:** The system only allows fetching product details if the order status is 'Delivered'; otherwise, an error message is returned.
  - **Authenticated Returns:** Logged-in users see a "Request Return" button directly within their Order Details page if the order is marked as "Delivered".
  - **Admin Approval Workflow:** Admins review requests in the "Return Requests" submenu. They can:
    - **Approve:** Must specify the condition for each item (Intact or Damage).
    - **Reject:** Must provide a rejection reason.
  - **Receiving Workflow:** Once approved, the request moves to a "Processing" state. After physically receiving the items, the admin marks the return as "Received".
  - **Stock & Sales Adjustment:** 
    - **Intact Items:** Automatically restocked (incremented) in the `products` or `product_variants` table. The product's `sales_count` is decremented.
    - **Damaged Items:** Automatically added to the `wastages` table for loss tracking. They are NOT restocked.
  - **Status Tracking:** Customers can track their return status (Pending, Approved, Rejected, Received) by entering their Order ID on the return page.
- **Data & Storage:**
  - **Related Tables:** `returns`, `return_items`, `wastages`, `products`, `product_variants`
  - **Storage:** Stores return headers, granular item conditions, proof images, and loss tracking for damaged units.
  - **Fetching:** `ReturnService` manages the complex workflow; `FlexSearch` is used for high-performance administrative indexing of requests and wastages.
- **Implementation Details:**
  - **`ReturnService`:** Orchestrates the entire lifecycle, including multi-item return logic, image handling via `HelperClass`, and the complex database transactions for receiving.
  - **Database Architecture:**
    - `returns`: Stores the main request metadata, status, and proof image.
    - `return_items`: Stores granular data for each item being returned, including its condition and received status.
    - `wastages`: A dedicated table to track products lost due to damage.
  - **Inventory Reports & Wastage Tracking:** 
    - **Stock & Batch Reports:** Accessible under the **Inventory** sidebar menu.
    - **Wastage Tracking:** The **Wastages** menu item (previously under Returns) has been moved to the **Inventory** section for better categorization, allowing admins to track damaged items and physical losses alongside other inventory reports.
  - **FlexSearch Integration:** Admin index pages for Requests, Returned Products, and Wastages all utilize `FlexSearch` for high-performance filtering and searching.
  - **UI/UX:** Uses AJAX for real-time order fetching on the client-side and a clean "Receiving Workflow" panel in the admin details view.

### 3.28 Role-Based Access Control (RBAC) Module
- **What:** A comprehensive security layer for managing administrative access using the `spatie/laravel-permission` package. It ensures that administrators can only perform operations explicitly granted to their roles.
- **How it Works:**
  - **Permission Seeding:** All system permissions (e.g., `products.view`, `orders.edit`, `admins.delete`) are defined in a central `PermissionSeeder`. Permissions follow a strict `module.operation` naming convention.
  - **Role Management:** Admins can create and edit roles. The Role form provides a grouped interface where permissions are categorized by menu (e.g., Category, Brand, Products) with "Check All" functionality for rapid assignment.
  - **Security Enforcement:**
    - **Middleware:** Routes are protected using the `permission` middleware (e.g., `->middleware('permission:products.create')`), ensuring backend security.
    - **Dynamic UI:** The Sidebar and Action Buttons (Add, Edit, Delete, Status Toggles) are wrapped in `@can` directives, automatically hiding restricted operations from the UI.
    - **Graceful Handling:** A custom `403 Access Denied` page provides clear feedback when an unauthorized operation is attempted.
  - **Profile Management:** Admin users can be assigned a single role and support profile image uploads for identification in the user list and sidebar.
- **Data & Storage:**
  - **Related Tables:** `roles`, `permissions`, `model_has_roles`, `role_has_permissions` (standard Spatie schema)
  - **Storage:** Many-to-many associations between roles, permissions, and admin models.
  - **Fetching:** Enforced via `RoleService` logic and standard Spatie middleware/Blade directives (`@can`).
- **Implementation Details:**
  - **`RoleService` & `AdminService`:** Handle the business logic for role synchronization, permission grouping, and user associations.
  - **Seeder Strategy:** `PermissionSeeder` ensures all granular permissions exist, while `RolePermissionSeeder` initializes the "Super Admin" role with full access.
  - **System-Wide Integration:** Enforcement is applied across all core modules: Products, Categories, Brands, Shipping, Orders, Returns, Promotions, Homepage, and Settings.

### 3.29 Order Cancellation/Rejection Remarks
- **What:** A feature to capture and display the reason when an order is cancelled or rejected by an administrator. This provides transparency to the customer about why their order was not fulfilled.
- **How it Works:**
  - **Admin Action:** When updating an order status to "Cancelled" or "Rejected", a mandatory "Reason/Remarks" field appears in the status update form.
  - **Data Storage:** The reason is stored in the `rejection_reason` column of the `orders` table.
  - **Customer Visibility:**
    - **Order Tracking:** The reason is displayed on the public order tracking page.
    - **Order Details:** The reason is visible in the customer's account under order details.
    - **Email Notification:** If "Email Notify" is enabled during the status update, the reason is included in the automated status update email.
- **Data & Storage:**
  - **Related Tables:** `orders`
  - **Storage:** Textual data stored in the `rejection_reason` column.
  - **Fetching:** Retrieved as part of the standard `Order` model during tracking lookups or email generation.
- **Implementation Details:**
  - **Validation:** `UpdateOrderStatusRequest` enforces that the reason is provided only for restorative statuses.
  - **Service Logic:** `OrderService::updateOrderStatus()` manages the persistence of the reason and ensures it is cleared if the status is moved to a non-restorative state (though status finality usually prevents this).
  - **Email Template:** The `status_update.blade.php` markdown template conditionally displays the reason using a Blade `@if` directive.

### 3.30 Google reCAPTCHA v2 Integration
- **What:** A static security layer for client-side authentication forms (Login and Registration) to prevent automated bot submissions.
- **How it Works:**
  - **Environment Configuration:** Site and Secret keys are managed via the `.env` file (`NOCAPTCHA_SITEKEY`, `NOCAPTCHA_SECRET`).
  - **Visual Challenge:** A reCAPTCHA v2 "I'm not a robot" widget is displayed on both the Login and Registration pages.
  - **Validation:** Form submission is blocked until the user successfully completes the reCAPTCHA challenge.
- **Data & Storage:**
  - **Related Tables:** N/A (Third-party service)
  - **Storage:** API keys are stored in the `.env` file for security.
  - **Fetching:** Tokens are validated against Google's API via the `anhskohbo/no-captcha` package during form submission.
- **Implementation Details:**
  - **Validation:** `LoginRequest` and `RegisterRequest` enforce the `captcha` validation rule.
  - **Global Assets:** The reCAPTCHA JavaScript is loaded via the master layout (`master.blade.php`) using `{!! NoCaptcha::renderJs() !!}`.
  - **Package:** Integrated `anhskohbo/no-captcha` for seamless reCAPTCHA v2 support in Laravel 12.

---

## 4. Frontend & UI Standardization Refinements
- **Cart & Checkout UI:** Utilizes an 8/4 Bootstrap grid split. Flexbox `align-items-stretch` ensures promotional banners explicitly match the height of summary cards.
- **Mini-Cart Simplification:** The off-canvas mini-cart has been streamlined by removing the redundant "Checkout" button, leaving only the "View Cart" button. This encourages users to review their items and select a shipping method on the full cart page before proceeding to checkout, reducing potential errors.
- **Mobile Header:** Implements a strict 3-6-3 column grid to ensure the hamburger menu, centered logo, and action icons (cart/user) remain perfectly aligned on small devices.
- **Search UI:** Streamlined to a pure text-input with FlexSearch autocomplete, omitting clunky category dropdowns for a cleaner aesthetic.
- **Navbar Simplification:** Removed redundant "Pages" and "Blog" menus from the main navigation. Replaced them with a consolidated "Account" dropdown menu that provides direct access to User Profile, Order History, Wishlist, and Authentication links (Login/Register/Logout) for both desktop and mobile views.
- **Button Standardization:** All action elements (Add to Cart, Track, Details, Start Shopping) strictly utilize core theme classes (e.g., Bootstrap `.btn` overrides combined with theme colors `#7AAACE` and `#333`, specific uppercase typography, and zero border-radius) ensuring 1:1 visual continuity.
- **Admin Avatar Standardization:** All admin profile images across the navbar, index tables, and forms are standardized to a fixed circular shape. This is achieved through a global CSS definition in `master.blade.php` that enforces `aspect-ratio: 1/1` and `object-fit: cover` for all `avatar-*` classes when used with the `rounded-circle` utility class.
- **Admin Theme Customization:** The sidebar (`main-nav`) and dark theme background colors have been updated to a custom deep blue shade (`#001F3D`). These overrides are applied globally in `master.blade.php` to maintain brand consistency across the administrative interface.
- **Sidebar Refinement:** The "Inventory" section has been flattened for better accessibility. Instead of a single collapsible dropdown, core inventory functions—Stock Report, Batch Tracking, Damaged Products, Wastages, Stock Adjustment, and Supplier RMA—are now individual top-level menu items under the "Inventory" title, each with its own unique icon.
- **Admin Assets:** Public admin CSS/JS directories renamed from `public/admin` to `public/admin_assets` to permanently resolve routing conflicts with the `Route::prefix('admin')` backend architecture.
- **Pagination UI Standardization:** All paginated index pages (both Admin and Client) are standardized to include "Showing X to Y of Z Results" text next to the pagination links. In the Admin panel, this is placed within a `card-footer` using a `d-flex justify-content-between` layout. On the Client side, it is centered above the pagination links to maintain visual balance.
- **Index Number Serialization Fix:** Standardized the row numbering across all administrative tables. To prevent "Trying to access array offset on int" errors, the system utilizes `HelperClass::indexNumberSerialization($data)` to calculate the starting serial for the current page, which is then incremented within the `@foreach` loop using `$sl++`. This approach ensures accurate, consecutive numbering across all paginated modules (Products, Orders, Inventory, etc.).
### 3.31 Inventory Management Onboarding (Warehouses & Suppliers)
- **What:** Foundation for an integrated inventory system, allowing administrators to manage storage locations (Warehouses) and external vendors (Suppliers).
- **How it Works:**
  - **Warehouse Management:** Admins create and manage physical storage locations. Each warehouse record stores a unique name and a detailed physical location.
  - **Dynamic Filtering:** The warehouse index page supports real-time searching and sorting via AJAX, integrated with `FlexSearch`.
  - **Warehouse Stock Details:** A "Stock Details" button in the warehouse index allows admins to view a dedicated inventory breakdown for each warehouse.
    - **Inventory Breakdown:** Lists all products, variants, and specific batch numbers currently stored in that warehouse.
    - **Quantity Tracking:** Displays both saleable and damaged quantities for each inventory record.
    - **Granular Serials:** Provides a modal-based interface to view individual physical unit (serial) details, including their condition and stock status, specifically within the selected warehouse.
  - **Supplier Management:**
    - **Onboarding:** Admins manage the vendor database (Name, Email, Mobile, Address).
    - **Supplier Details & History:** A comprehensive details view for each vendor that displays:
      - **Vendor Profile:** Core contact information.
      - **Performance Analytics:** Real-time Average Performance Score derived from all delivered POs.
      - **Purchase History:** A paginated list of all Purchase Orders associated with the supplier, showing their current status, total value, and specific fulfillment score.
- **Data & Storage:**
  - **Related Tables:** `warehouses`, `suppliers`, `purchase_orders`
  - **Storage:** Metadata for physical locations and vendor contact details are stored in standard relational columns.
  - **Fetching:** `InventoryService` manages CRUD and details retrieval; `Supplier` model provides a `purchaseOrders` relationship for history tracking.
- **Implementation Details:**
  - **Architecture:** Follows the strict Service Layer pattern. `InventoryService::getSupplierWithOrders()` centralizes the data retrieval for the details view.
  - **UI Integration:** The Supplier index features a "View Details" action button (Eye icon). The details page uses a responsive 2-column layout to separate profile info from the historical order table.
  - **Validation:** `WarehouseRequest` and `SupplierRequest` enforce strict data integrity.
  - **Search & Filtering:** Both index pages utilize `FlexSearch` for real-time searching and AJAX-driven sorting.

### 3.32 Purchase Order (PO) Module & Refinement
- **What:** A comprehensive procurement system for managing product intake, refined with warehouse targeting, batch tracking, and individual serial number management.
- **How it Works:**
  - **Warehouse Targeting:** Every Purchase Order is assigned a target Warehouse upon creation. This dictates where the inventory will be physically stored once received.
  - **Itemization:** Admins add products or variants with specific `order_quantity` and `unit_cost`.
  - **Status Management:** 
    - At creation and edit, only **Draft** and **Sent** statuses are available. This ensures that the **Delivered** status is only reachable through the formal receiving process, which is necessary for accurate inventory and serial tracking.
  - **Refined Receiving Workflow:** When an order is "Sent", the receiving process facilitates granular tracking:
    - **Global Batch Management:** A single `batch_number` is assigned to the entire receipt. This creates a `Batch` header record (containing the `supplier_id`) that groups all items received in that shipment.
    - **Batch Items:** Individual quantities for each product/variant are stored in the `batch_products` table, linked to the main `Batch` header.
    - **Serial Number Tagging:** The system uses a "tag-style" UI (Select2) for entering individual product serial numbers. 
      - **Literal Parsing:** Serial numbers containing hyphens (e.g., `SN-123-ABC`) are treated as literal strings. The system no longer supports or expands numeric ranges (e.g., `SN001-SN005`) automatically, as each unit is now tracked as an individual tag to prevent accidental expansion of hyphenated serials.
    - **Granular Serials:** Administrators can enter separate lists of serial numbers for **Received** (Good) and **Damaged** products. 
 
      - **UI Enhancement:** The serial number input area is designed to be compact, becoming scrollable after 4 tags are added to maintain page layout integrity.
      - **Validation:** The system enforces that the number of entered serials must exactly match the quantity specified for each category (Received or Damaged) if serial tracking is used.
      - **Storage:** These serials are stored in a dedicated `batch_serials` table. Received serials are marked as `in-stock`, while damaged serials are marked as `damaged`.
    - **Damaged Goods Handling:** Admins specify `Received` and `Damaged` quantities. To ensure accuracy and speed, the interface includes **auto-calculation logic**:      - Entering a `Received` quantity automatically calculates and fills the remaining `Damaged` quantity based on the total ordered amount.
      - Entering a `Damaged` quantity automatically updates the `Received` quantity.
      - This logic enforces that the sum of Received and Damaged items never exceeds the total quantity originally ordered.
      - `Received` items move to the PO's target warehouse, while `Damaged` items are tracked separately under a "Damaged" batch header.
  - **Automated Inventory Synchronization:**
    - **Batch-Level Tracking:** The system creates unique records in the `inventory_levels` table for each batch received. This allows for precise tracking of exactly how much of a specific batch is remaining in a warehouse.
    - **Stock & Ledger:** Updates total product stock, warehouse-batch inventory levels (`inventory_levels`), and creates detailed batch product records. Every movement triggers a **Stock Ledger** entry for movement traceability.
- **Data & Storage:**
  - **Related Tables:** `purchase_orders`, `purchase_order_items`, `batches`, `batch_products`, `batch_serials`, `inventory_levels`, `stock_ledgers`
  - **Storage:** Procurement data is stored across a complex relational hierarchy; serial numbers use a specialized tagging table for individual unit tracking; **unit costs are stored at the Batch Product level**.
  - **Fetching:** `PurchaseOrderService` executes massive atomic transactions (`DB::transaction`) to synchronize data across 7+ tables during the receiving phase.
- **Implementation Details:**
  - **`PurchaseOrderService`:** Manages the complex multi-table transaction for receiving, which includes creating a `Batch` header, multiple `BatchProduct` and `BatchSerial` records, updating `InventoryLevel`, and logging to the `StockLedger`.
  - **Schema Optimization:** Removed `serial_numbers` from the `purchase_order_items` table and `unit_cost` from `products`/`variants` to eliminate data redundancy and centralize costing at the batch level.
  - **Validation:** `PurchaseOrderReceiveRequest` ensures a single global `batch_number` is provided and that serial counts match the total quantities per item.

### 3.33 Stock Ledger & Audit Trail
- **What:** A centralized, immutable transaction log that tracks every stock movement (increase or decrease) across the entire system, with support for individual physical unit (serial) tracking.
- **How it Works:**
  - **Automated Logging:** Any action that modifies stock (PO Receipt, Sales, Returns, Adjustments, Damage Entry) automatically triggers a ledger entry.
  - **Granular Data:** Each entry records the product, variant, warehouse, batch, supplier, and change quantity.
  - **Aggregate Logging:** Movements are logged as a single aggregate transaction with the total quantity (e.g., -3 for a 3-unit RMA return), providing a clean and concise audit trail.
  - **Cost Tracking:** Financial data (unit cost) is resolved dynamically by joining with the `batch_products` table via the `batch_id` associated with the ledger entry.
- **Data & Storage:**
  - **Related Tables:** `stock_ledgers`, `products`, `product_variants`, `warehouses`, `batches`, `batch_products`, `batch_serials`
  - **Storage:** Relational tracking using UUIDs for audit integrity; includes a `batch_serial_id` column for 1:1 unit traceability.
  - **Fetching:** `InventoryService::getStockLedger()` provides a searchable, filterable report accessible via the Admin Sidebar.
- **Implementation Details:**
  - **`InventoryService::logStockChange()`:** Centralized recording engine updated to support `batch_serial_id`.
  - **Module Integration:** Updated `PurchaseOrderService`, `OrderService`, `SupplierRmaService`, `StockAdjustmentService`, and `DamageEntryService` to loop and log individual serials when applicable.

### 3.34 Advanced Inventory Tracking (Inventory Levels)
- **What:** Granular, batch-aware tracking of products within specific warehouses, including proactive stock management and accurate procurement costing.
- **How it Works:**
  - **Batch Integration:** Stock is no longer just tracked by warehouse; it is tracked by **Warehouse + Batch**. The `inventory_levels` table stores `current_quantity` for every unique batch-warehouse-product combination.
  - **Batch-Level Costing:** The `unit_cost` is now stored at the **Batch Product** level (`batch_products` table). This allows the system to track different procurement costs for the same item across different shipments, enabling more accurate FIFO/LIFO reporting.
  - **Total Stock Synchronization:** The system maintains a "Saleable Stock" model.
    - **Global Stock:** The `stock` field in the `products` and `product_variants` tables represents the **Total Saleable System Stock** (the sum of all quantities across non-quarantine warehouses).
    - **PO Receipt:** When a shipment is received, it increments the global `stock` only by the **Good/Received Quantity**. Damaged items are tracked in `inventory_levels` (Quarantine) and the Stock Ledger but are excluded from the global `stock` availability.
    - **Sales:** When an item is sold, it decrements the global `stock`.
    - **Consistency:** This tracking ensures that the customer-facing catalog only shows items available for purchase, while the administrative reporting module provides visibility into both saleable and quarantined physical units.
  - **Customizable Thresholds:** Supports `min_stock_override` per record, allowing for fine-tuned stock alerts that override global product limits.
  - **Alert Management:** Tracks `last_alert_sent` to prevent notification spam when stock levels fall below thresholds.
- **Data & Storage:**
  - **Related Tables:** `inventory_levels`, `batches`, `warehouses`, `products`
  - **Storage:** Granular quantities are stored at the unique intersection of product, warehouse, and batch.
  - **Fetching:** `InventoryService` lookups utilize composite keys (Product/Warehouse/Batch) to ensure precise inventory resolution and saleable stock calculations.
- **Implementation Details:**
  - **Service Logic:** Inventory lookups now incorporate the `batch_id` to ensure accurate fulfillment.

### 3.35 Warehouse Stock Limits (REQ-104)
- **What:** A granular alerting system that allows administrators to set low-stock thresholds at both global and warehouse levels.
- **How it Works:**
  - **Global Limit:** A single threshold applied to the total saleable stock of a product/variant across all warehouses.
  - **Warehouse Limit:** Specific thresholds defined for individual warehouses via a dedicated `warehouse_stock_limits` table.
  - **UI/UX:** The product form features a modal-driven interface where admins can select a warehouse and assign a minimum stock limit. Multiple warehouse-specific limits can be added per item.
  - **Dynamic Thresholding:** If a product is set to "Warehouse" limit type, the system monitors each warehouse's stock against its specific threshold. If set to "Global", it monitors the sum of all saleable stock.
- **Data & Storage:**
  - **Related Tables:** `warehouse_stock_limits`, `products`, `warehouses`
  - **Storage:** Warehouse-specific numeric thresholds are stored in a dedicated relational table.
  - **Fetching:** `DashboardService` joins `inventory_levels` with `warehouse_stock_limits` to accurately identify which products are below their required levels in specific physical locations.
- **Implementation Details:**
  - **Database:** Uses the `warehouse_stock_limits` table (`product_id`, `product_variant_id`, `warehouse_id`, `min_stock`).
  - **Service Logic:** `DashboardService` joins `inventory_levels` with `warehouse_stock_limits` to accurately identify which products are below their required levels in specific locations.
  - **Validation:** Enforced via `ProductRequest` to ensure non-negative integer thresholds.

### 3.36 Inventory Reports (Stock & Damaged Products)
- **What:** A comprehensive suite of analytical views providing real-time visibility into physical stock distribution, batch tracking, and quarantine inventory.
- **How it Works:**
  - **Consolidated Stock Report:** A centralized view showing exactly where every physical unit is stored. It lists products, variants, warehouses, and the specific **Batch Number** for each stock record.
    - **Integrated Batch Tracking:** The report includes batch-level tracking by default. Admins can search for specific batch numbers directly within the main stock search bar.
    - **Quarantine Filtering:** By default, this report excludes all products stored in quarantine/damaged warehouses to ensure the "Stock" view represents saleable inventory.
    - **Low Stock Highlighting:** Quantities that fall below their specific `min_stock_override` are visually highlighted in red.
  - **Damaged Products Report:** A dedicated report for inventory stored in quarantine warehouses or specifically marked as damaged.
    - **Quarantine Focus:** Specifically filters for records where the warehouse `is_quarantine` flag is true OR `damaged_quantity` is greater than 0.
    - **Batch-Centric Tracking:** The report is prioritized by **Batch Number** to assist in supplier tracing. It explicitly excludes saleable quantities to focus only on losses.
    - **Granular Details:** Clicking "Details" on a damaged record opens a dedicated view showing only the damaged quantity and a list of physical serial numbers currently marked with a `damaged` status.
- **Data & Storage:**
  - **Related Tables:** `inventory_levels`, `products`, `product_variants`, `warehouses`, `batches`
  - **Storage:** Relational views derived from current stock distributions.
  - **Fetching:** `InventoryReportController` uses specialized Eloquent queries with `whereHas('warehouse', fn($q) => $q->where('is_quarantine', ...))` to generate segregated reports.
- **Implementation Details:**
  - **`InventoryReportController`:** Manages the specialized reporting routes for both Stock and Damaged products.
  - **High-Performance Filtering:** Both reports support real-time searching (Product, Variant, or Batch) and filtering by Warehouse.
  - **UI/UX:** Uses AJAX-driven tables with URL synchronization, allowing admins to bookmark specific filtered reports. Both reports utilize a shared `partials/table.blade.php` for consistent data presentation.

### 3.37 Supplier RMA (Return to Vendor) Module
- **What:** A specialized module for administrators to return damaged or defective products back to their respective suppliers.
- **How it Works:**
  - **Dynamic Selection:** Admins can create a return request by selecting a Supplier or a specific "Delivered" Purchase Order.
  - **Batch & Serial Integration:** Once a supplier/PO is selected, the system dynamically fetches all associated batches that contain damaged items (`total_damaged_qty > 0`).
    - **Granular Control:** Admins can enter return quantities for each batch. Entering `0` skips the product.
    - **Serial Tracking:** For serial-tracked products, admins click "Select Serials" to open a modal with checkboxes for individual physical units.
    - **Quantity Logic:** Selecting serials automatically locks the quantity to the selection count. If no serials are chosen, the quantity defaults to the total damaged pool for that batch.
  - **Security & Integrity:** The system automatically filters out products and serial numbers that are already part of another active RMA to prevent duplicate returns.
  - **Status Workflow:** RMAs follow a strict lifecycle: `Pending` -> `Approved` -> `Shipped` -> `Closed`.
  - **Inventory Finalization (Closing):** When an RMA is moved to the `Closed` status (protected by a custom "Close RMA" SweetAlert confirmation), the system executes:
    - **Precise Decrementation:** Instead of a reset, the system specifically **decrements** the `total_damaged_qty` in `batches`, `batch_products`, and **`inventory_levels`** based on the RMA count.
    - **Serial Status Update:** The `product_status` of returned serials is changed to `damaged_return` and their `stock_status` to `returned`.
    - **Financial Audit:** A `StockLedger` entry is created (`transaction_type: RTV_Dispatch`, `section_name: Supplier RMA`).
- **Data & Storage:**
  - **Related Tables:** `supplier_rmas`, `rma_items`, `batch_serials`, `stock_ledgers`, `batches`, `batch_products`, `inventory_levels`
  - **Storage:** RTV dispatch metadata, line items, and updated inventory/serial status flags are persistent in the database.
  - **Fetching:** `SupplierRmaService` manages the complex inventory synchronization; jQuery AJAX lookups dynamically fetch filtered, non-duplicate damaged batches and serials.
- **Implementation Details:**
  - **`SupplierRmaService`:** Orchestrates creation, automated vendor emails, and the atomic transaction for multi-table inventory adjustment upon closing.
  - **AJAX-Driven UI:** Uses Select2 and custom jQuery modals for dynamic data loading. Ensures JSON arrays are returned via `->values()` to prevent browser errors.
  - **Professional Details View:** Displays grouped items with modal-based serial tracking and provides both "Creation Date" and "Completed/Last Updated" timestamps for audit visibility.
  - **Automation:** Integrated with `SupplierRmaMail` to automatically dispatch detailed return lists to vendors upon request creation.

### 3.38 Stock Adjustment Module
- **What:** A manual inventory entry system that allows administrators to add stock directly into warehouses and batches without requiring a formal Purchase Order.
- **How it Works:**
  - **Manual Entry:** Admins can select a target Warehouse and enter a Batch Number manually.
  - **Multi-Item Support:** The interface allows adding multiple products or variants in a single adjustment session.
  - **Cost & Serial Tracking:** For each item, admins specify the quantity, unit cost, and optional comma-separated serial numbers.
  - **Automated Synchronization:** Upon processing, the system performs a multi-table update:
    - **Batch Management:** Creates or updates the specified `Batch` and its associated `BatchProduct` records.
    - **Inventory Levels:** Increments the `current_quantity` in the `inventory_levels` table for the selected warehouse/batch.
    - **Global Stock:** Updates the total saleable stock in the `products` and `product_variants` tables.
    - **Serial Tracking:** If serials are provided, they are inserted into `batch_serials` with an `in_stock` status and `good` condition.
    - **Audit Log:** Generates a `StockLedger` entry with `transaction_type: Manual_Adjustment` for full financial and movement traceability.
- **Data & Storage:**
  - **Related Tables:** `stock_adjustments`, `stock_adjustment_items`, `batches`, `batch_products`, `batch_serials`, `inventory_levels`, `stock_ledgers`, `products`
  - **Storage:** Adjustment headers, detailed line items, and updated inventory counts are stored across standard relational tables.
  - **Fetching:** `StockAdjustmentService` manages the complex transactional logic; `FlexSearch` provides real-time administrative indexing.
- **Implementation Details:**
  - **`StockAdjustmentService`:** Centralizes the atomic transaction required to maintain data integrity across the entire inventory system.
  - **Dynamic UI:** Features a repeater-style form for adding multiple items with live product/variant selection.
  - **Financial Logic:** Automatically calculates total cost per line item and for the entire adjustment for ledger reporting.

### 3.39 Damage Entry (Warehouse Wastage) Module
- **What:** A specialized module to manually record products that have been damaged within the warehouse, distinct from damages identified during PO receipt.
- **How it Works:**
  - **Contextual Selection:** Admins select a Warehouse, then a specific Batch available in that warehouse, and finally the Product/Variant to be marked as damaged.
  - **Mandatory Serial Tracking:** If the selected product/batch has associated serial numbers, the system forces the admin to select the specific physical units being discarded via a modal-based checkbox interface.
  - **Inventory Synchronization:**
    - **Quantity Transfer:** The system decrements the `current_quantity` (saleable) and increments the `damaged_quantity` in the `inventory_levels` table.
    - **Batch Updates:** Similarly updates the `total_saleable_qty` and `total_damaged_qty` in the main `batches` and `batch_products` tables.
    - **Serial Status:** Selected serials are updated to `product_status: damaged` and `stock_status: wastage`.
  - **Audit Integrity:** Every entry creates a record in the `wastages` table and logs a transaction in the `StockLedger` with `transaction_type: warehouse_damage`.
- **Data & Storage:**
  - **Related Tables:** `wastages`, `batch_serials`, `inventory_levels`, `batches`, `batch_products`, `stock_ledgers`
  - **Storage:** Standard relational columns for warehouse/batch linking; updated status flags for physical unit tracking.
  - **Fetching:** `DamageEntryService` handles the atomic transition logic; AJAX endpoints provide real-time, context-aware filtering for warehouses, batches, and products.
- **Implementation Details:**
  - **`DamageEntryService`:** Manages the complex multi-table transaction to ensure stock consistency after a damage event.
  - **UI Integration:** The "Damage Entry" button is integrated into the main Wastage index for easy access.
  - **Validation:** `DamageEntryRequest` ensures that quantities do not exceed available saleable stock and that serial selections are mandatory when applicable.

### 3.40 Supplier Performance Scoring
- **What:** An automated performance evaluation system for vendors based on Purchase Order (PO) fulfillment accuracy and timeliness.
- **How it Works:**
  - **Automated Calculation:** A performance score and fulfillment subtotals are automatically calculated and stored for every Purchase Order when its status is updated to "Delivered" via the formal receiving process.
  - **Calculation Logic (Detailed Formulas):**
    - The total score is out of **100 points**, calculated using two primary metrics:
    - **1. Delivery Score (40% Weight):**
      - **Formula:** `IF (Actual Received Date <= Expected Delivery Date) THEN 40 ELSE 0`
      - **Logic:** If the shipment arrives on or before the deadline set during PO creation, the supplier receives the full 40 points. If late, or if no expected date was provided, they receive 0 points for this component.
    - **2. Quality Score (60% Weight):**
      - **Formula:** `(Total Received Qty / (Total Received Qty + Total Damaged Qty)) * 60`
      - **Logic:** This measures the ratio of "Good" products vs. "Total" products delivered. If 100% of the items are intact, the supplier receives 60 points. If 10% are damaged, they receive `(0.9 * 60) = 54` points.
    - **3. Final Performance Score:**
      - **Formula:** `Total Score = Delivery Score + Quality Score`
      - **Example:** A PO delivered on time (40 pts) with 5% damaged items (57 pts) results in a total performance score of **97.00%**.
  - **Fulfillment Subtotals:** To ensure high-performance reporting, the system calculates and stores `total_received_qty` (good products) and `total_damaged_qty` directly in the `purchase_orders` record.
  - **Supplier Analytics:** The system maintains a running average of performance scores across all delivered POs for each supplier.
  - **Visibility:** 
    - **PO Details:** Displays the performance score and a "Fulfillment Summary" badge showing the breakdown of Good vs. Damaged totals.
    - **Supplier List:** The main supplier index features an "Avg Performance" column with color-coded badges (Green for 80+, Yellow for 50+, Red for <50) and star icons for rapid vendor assessment.
- **Data & Storage:**
  - **Related Tables:** `purchase_orders`, `suppliers`
  - **Storage:** `performance_score` (decimal), `total_received_qty` (int), and `total_damaged_qty` (int) are stored directly in the `purchase_orders` table.
  - **Fetching:** `PurchaseOrderService` handles the calculation and storage during receipt; `Supplier` model utilizes an Eloquent accessor (`average_performance_score`) to compute real-time averages across all its delivered orders.
- **Implementation Details:**
  - **Architecture:** Follows the strict Service Layer pattern. Calculation occurs within a `DB::transaction` in `PurchaseOrderService::receivePurchaseOrder`.
  - **UI Integration:** Uses Bootstrap 5 badges and Iconify icons for consistent, modern data presentation in both the order summary and vendor management interfaces.

### 3.41 Order Fulfillment & Inventory Integration
- **What:** A granular inventory allocation and tracking system that connects customer orders with multiple specific warehouse batches and physical unit serial numbers, while tracking procurement costs.
- **How it Works:**
  - **Restricted Status Workflow:** Orders follow a strict logical progression: `Pending` → `Processing` → `Shipped` → `Out for Delivery` → `Delivered`.
  - **Shipped Status (Dynamic Allocation):** When an admin moves an order to `Shipped`, they must allocate inventory.
    - **Multi-Batch Support:** A single order item can be fulfilled from **multiple warehouses** and/or **multiple batches**. Admins can split the total item quantity across different batches via a dynamic UI.
    - **Warehouse & Batch Selection:** Admins select fulfillment locations and specific batches. The UI validates that the total allocated quantity across all rows exactly matches the ordered quantity.
    - **Procurement Cost Calculation:** The system automatically fetches the `unit_cost` from each selected `batch_products` record. It calculates a `subtotal_cost` per batch and a `total_cost` per item and order, allowing for precise profit/loss reporting based on actual procurement history.
    - **Modal-Based Serial Tracking:** For serial-tracked items, admins select specific units for each batch allocation. Serial status is updated to `shipped`.
  - **Delivered Status (Finalization):** When marked as `Delivered`:
    - **Stock Deduction:** The system decrements physical stock (`inventory_levels`, `batches`, `batch_products`) based on the granular `ordered_product_batches` records.
    - **Global Stock Sync:** Global `products` or `variants` stock fields are updated.
    - **Ledger Integration:** Logs **aggregate stock ledger entries** (one per batch allocation) for movement traceability.
- **Data & Storage:**
  - **Related Tables:** `orders`, `order_items`, `ordered_product_batches`, `batch_serials`, `inventory_levels`, `stock_ledgers`, `batches`, `batch_products`
  - **Storage:** The `ordered_product_batches` table serves as the granular linkage between orders and specific inventory units.
  - **Financial Audit:** `orders` and `order_items` tables store `total_cost` (procurement sum).
- **Implementation Details:**
  - **UI/UX:** The Order Details page features a dynamic allocation interface allowing rows to be added/removed per item. It includes real-time calculation of allocated quantities.
  - **Fulfillment Visibility:** Once allocated, the specific Warehouse, Batch, and Serial Numbers for every split allocation are displayed directly in the items table.

### 4.0 Inventory & Stock Calculation Logic
This section defines the mathematical and logical rules governing stock levels throughout the application lifecycle.

#### 4.1 Stock Definitions
- **Total System Stock:** The global sum of all saleable units across all warehouses.
  - *Storage:* `products.stock` or `product_variants.stock`.
- **Saleable Stock (Current Qty):** High-quality units available for immediate sale.
  - *Storage:* `inventory_levels.current_quantity`, `batch_products.saleable_qty`.
- **Damaged Stock:** Units received in poor condition or moved to quarantine. These are **excluded** from saleable counts.
  - *Storage:* `inventory_levels.damaged_quantity`, `batch_products.damaged_qty`.

#### 4.2 Calculation Formulas
| Event | Action | Formula |
| :--- | :--- | :--- |
| **PO Receipt** | Increase Stock | `Saleable Qty = Saleable Qty + Received Qty (Good)` |
| **PO Receipt** | Track Damage | `Damaged Qty = Damaged Qty + Received Qty (Damaged)` |
| **Order Placed** | No Change | *Stock is reserved but not deducted from database levels.* |
| **Order Shipped** | No Change | *Serials marked 'shipped'; Saleable stock level remains same.* |
| **Order Delivered** | Decrease Stock | `Saleable Qty = Saleable Qty - Order Qty` |
| **Order Cancelled** | Release Stock | `Serials status = 'in_stock'; OrderItem linkage removed.` |
| **Damage Entry** | Transfer | `Saleable Qty = Saleable Qty - Qty`; `Damaged Qty = Damaged Qty + Qty` |

#### 4.3 Transactional Integrity
All stock movements are executed within database transactions (`DB::transaction`). A movement is only valid if:
1. The requested warehouse has sufficient `current_quantity`.
2. The specific batch selected contains the requested quantity.
3. (If applicable) The number of selected serial units exactly matches the `order_item.quantity`.

#### 4.4 Financial Impact (Stock Ledger)
Every physical decrement of stock (Sale, Damage, Wastage) triggers a ledger entry:
- **Unit Cost:** Derived from the specific Batch or Product record at the time of movement.
- **Total Movement Cost:** `Quantity Change * Unit Cost`. (Negative for sales/wastage).

---
*Note: This documentation is the source of truth for the smart-ecom project and is updated as the project evolves.*
