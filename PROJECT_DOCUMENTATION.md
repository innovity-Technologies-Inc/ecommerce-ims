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
- **Implementation Details:** 
  - Slugs are automatically generated via Eloquent mutators or Service logic on creation.
  - Deleting a category safely handles child relationships (e.g., setting parent_id to null or cascading deletes, depending on schema constraints).

### 3.4 Product & Inventory System
- **What:** The core catalog engine supporting complex pricing, variants, and marketing flags.
- **How it Works:**
  - **Flexible Pricing Engine:** A product can have a `base_price` and multiple `ProductVariant` records. If variants exist, their specific pricing overrides the base price.
  - **Marketing Flags:** Boolean columns in the `products` table (e.g., `is_new`, `is_featured`, `is_hot_deal`) dictate where the product appears on the frontend.
  - **Multi-Image Gallery:** Handled via a dedicated `product_images` table, allowing infinite images per product with one designated as the primary thumbnail.
  - **Global Minimum Stock:** Each product can have a `min_stock_global` threshold. This is used to trigger "Low Stock" alerts on the dashboard if individual variant stock falls below this number. It acts as a per-product override for the system-wide `low_stock_limit` setting.
- **Implementation Details:** 
  - `ProductService` orchestrates the creation of the product, uploads the primary image, iterates through variant arrays to create `ProductVariant` rows (handling SKU and stock), and stores secondary images in the `product_images` table.

### 3.5 Customer Shop & Frontend Filtering
- **What:** The public-facing product catalog with advanced search, filtering, and sorting capabilities.
- **How it Works:**
  - **Sidebar Filtering:** Users can filter by Categories, Brands, and Price. The backend captures these query parameters (e.g., `?category=electronics&min_price=100`) and dynamically builds Eloquent queries in `FrontendController`/`ProductService`.
    - **Advanced Price Filtering:** The system implements a robust, variant-aware price filter. It prioritizes the `discount_price` if available; otherwise, it falls back to the `regular_price` (selling price). The filter explicitly checks both the base product and all associated variants. If a product's base price is `0` or `NULL` (indicating it is priced via variants), the product is still correctly included if any of its variants fall within the filtered range.
  - **Accurate Price Sorting:** The sorting logic ("Price: Low to High" and "Price: High to Low") utilizes an "effective price" calculation. To prevent inaccuracies caused by `0` or `NULL` base prices, the system uses a `DB::raw` `CASE` statement. It first checks for a valid base price (preferring discounts); if none exists, it scans all attached variants to find the lowest available price. This resulting `sort_price` guarantees consistent ordering across complex hybrid products.
  - **Global Search:** Powered by `FlexSearch`. When a user types in the navbar, the query is passed to the FlexSearch engine which indexes multiple tables (Name, Brand, Category) to return rapid, highly relevant results without heavy `LIKE %...%` queries.
  - **Variant Selection:** On the product details page, selecting different variants dynamically updates the displayed price and available stock using JavaScript/AJAX.

### 3.6 Wishlist System
- **What:** A persistent feature allowing logged-in users to save products for later.
- **How it Works:**
  - Authenticated users click a heart icon, triggering an AJAX POST request to `WishlistController`.
  - $this->WishlistService checks if the item is already saved; if not, it attaches the `product_id` to the `user_id` in the `wishlists` table.
- **Implementation Details:** 
  - The wishlist view dynamically calculates the "Net Price" of items, automatically resolving whether the product should display its base price or its lowest available variant price.

### 3.7 Site Settings & Dynamic Configuration
- **What:** Admin-controlled global settings for SEO, Branding, and Contact Information.
- **How it Works:** 
  - **General Settings:** Stores business name, logos (dark/light), favicons, and currency in the database.
  - **Contact Settings:** Stores company name, email, phone number, physical address, a Google Maps integration link (`map_link`), and dynamic social media URLs (Facebook, Instagram, TikTok, X, Threads, LinkedIn, WhatsApp, YouTube) with visibility toggles.
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
- **Implementation Details:** 
  - Supports Cash on Delivery (COD).
  - Triggers an `OrderConfirmationMail` to the customer immediately upon successful creation.

### 3.10 Order History & Guest Tracking
- **What:** Customer-facing interfaces to view past purchases and track order status.
- **How it Works:**
  - **Authenticated Users:** Can navigate to "My Orders" in their account dashboard. `OrderService::getUserOrders()` fetches paginated results explicitly tied to their `user_id`.
  - **Guest Tracking:** A public "Track Order" page allows anyone with a valid `order_id` to view the status. `OrderService::trackOrderById()` fetches the order and its items.
- **Implementation Details:**
  - **Visual Progress Bar:** The frontend Blade template uses a calculated index array `['Pending', 'Processing', 'Out for Delivery', 'Delivered']` to dynamically highlight a CSS progress bar based on the current `order_status`.

### 3.11 Invoice Management Module
- **What:** Automated and manual generation of printable/downloadable PDF-style invoices.
- **How it Works:**
  - **Admin Side:** Admins view an order and click "Generate Invoice". `OrderService::generateInvoice()` creates a sequential number (`INV-YYYYMMDD-0001`) and stamps the `invoice_date`. Admins can subsequently "Regenerate" (updates the date) or "View".
  - **Client Side:** Customers click "Download Invoice" on their order details page. If the invoice wasn't generated by the admin yet, the system auto-generates it on the fly to prevent errors.
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

- **Note:** This automation ensures that time-sensitive features like Flash Sale discounts are reset precisely when they expire.

### 3.21 Frontend Refactoring & Public Invoice
- **What:** Architectural cleanup of the Frontend module and expansion of order tracking features.
- **How it Works:**
  - **Thin Controllers:** `FrontendController` was refactored to remove all business logic, moving it to `FrontendService`.
  - **Service Layer Pattern:** `FrontendService` now centralizes all product filtering (Category, Brand, Price, Search), related products logic, and sorting.
  - **Form Requests:** `ProductFilterRequest` and `TrackOrderRequest` handle all input validation, ensuring clean and secure data flow.
  - **Public Invoice Access:** Customers can now print invoices directly from the Order Tracking results without needing an account. This is enabled by a `publicInvoice` route that automatically generates an invoice if it doesn't already exist.
- **Implementation Details:**
  - **Refined Price Filter:** The pricing logic in `FrontendService` prioritizes `discount_price` and correctly handles products whose price is defined solely in variants (0/NULL base price) by performing a nested variant check.
  - **Security:** While public, the invoice access is protected by the unique Order ID requirement and is read-only.

### 3.21 Product Stock Display
- **What:** Real-time visibility of inventory levels for both admins and customers.
- **How it Works:**
  - **Admin Interface:** The product details page in the admin panel now displays "Base Stock" for simple products and a detailed breakdown of stock for each variant in the variation table.
  - **Client Interface:** The product details page features an "Availability" badge that dynamically updates based on the selected variant.
  - **Dynamic Interactivity:** Integrated with JavaScript, selecting a variant instantly updates the stock status (e.g., "15 In Stock" vs "Out of Stock").
- **Implementation Details:**
  - **Unified Stock Tracking:** Supports the hybrid inventory model where stock can be managed globally at the product level (Base Stock) or specifically per variant.
  - **Auto-Disable Cart:** The "Add to Cart" button is automatically hidden if the selected item/variant is out of stock, ensuring a smooth customer experience.

### 3.22 Automated Stock Management
- **What:** Automatic synchronization of inventory levels during the order lifecycle.
- **How it Works:**
  - **Deduction on Placement:** When a customer places an order (`placeOrder`), the system automatically decrements the stock for each purchased item (either base stock or variant stock) based on the quantity ordered.
  - **Restoration on Cancellation/Rejection:** If an admin or system update changes an order's status to `Cancelled` or `Rejected`, the system automatically increments the stock back to its previous levels.
  - **Status Reversibility:** If an order is moved from a restorative status (`Cancelled`/`Rejected`) back to an active status (e.g., `Pending`), the stock is intelligently re-deducted.
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
  - **Proactive Inventory Management:** Automatically identifies and lists products with variants that have fallen below a customizable "Low Stock Limit". This limit can be adjusted in the **General Settings** module.
  - **Business Intelligence:** Displays a "Best Selling Products" leaderboard based on the lifetime `sales_count` attribute, highlighting top-performing inventory.
- **Implementation Details:**
  - **`DashboardService`:** Centralizes all complex aggregation logic. It utilizes optimized Eloquent queries with `SUM()` and `COUNT()` aggregates to ensure high performance even as the database grows.
  - **Dynamic Charting:** Monthly sales data is retrieved using `DB::raw` with `MONTH()` groupings and passed to an area-style ApexChart via JSON encoding in the Blade view.
  - **Customizable Alerts:** The "Low Stock" threshold is stored in the `general_settings` table and injected into the service layer, allowing the admin to define what constitutes a stock emergency.
  - **Integrated Navigation:** The dashboard provides direct links to "Restock" low-stock items, filtered order lists, and a dedicated **Best Selling Products** report.

### 3.25 Best Selling Products Report (Admin)
- **What:** A specialized reporting page that identifies top-performing products over different time periods.
- **How it Works:**
  - **Time-Based Filtering:** Admins can filter the best-selling list by "Monthly" (current month), "Yearly" (current year), or "All Time".
  - **Delivered Orders Only:** To ensure accuracy, the sales count is calculated by summing quantities from `order_items` that belong to orders with a `Delivered` status.
  - **Live Dashboard Integration:** The main dashboard features two quick-glance cards for Monthly and Yearly best sellers, each with a "View All" button that redirects to the full paginated report with the corresponding filter pre-applied.
- **Implementation Details:**
  - **`DashboardService::getBestSellingProductsPaged()`:** Centralizes the logic using optimized joins and `SUM()` aggregates.
  - **AJAX-Driven:** The report supports instant filtering and pagination without full page reloads, maintaining URL state via `history.pushState`.

### 3.25 Global Wishlist Logic
- **What:** Centralized implementation of wishlist functionality to ensure consistent behavior across all storefront pages.
- **How it Works:** 
  - **Global Form & Function:** The hidden form and `addToWishlist` JavaScript function are placed within `master.blade.php`, making them available on the homepage, shop page, and product details pages.
  - **Functional Cart Integration:** The wishlist page includes a context-aware "Add to Cart" button that intelligently handles simple products, variant products, and discontinued items.
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
- **Implementation Details:**
  - **`ReturnService`:** Orchestrates the entire lifecycle, including multi-item return logic, image handling via `HelperClass`, and the complex database transactions for receiving.
  - **Database Architecture:**
    - `returns`: Stores the main request metadata, status, and proof image.
    - `return_items`: Stores granular data for each item being returned, including its condition and received status.
    - `wastages`: A dedicated table to track products lost due to damage.
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
- **Admin Assets:** Public admin CSS/JS directories renamed from `public/admin` to `public/admin_assets` to permanently resolve routing conflicts with the `Route::prefix('admin')` backend architecture.
- **Pagination UI Standardization:** All paginated index pages (both Admin and Client) are standardized to include "Showing X to Y of Z Results" text next to the pagination links. In the Admin panel, this is placed within a `card-footer` using a `d-flex justify-content-between` layout. On the Client side, it is centered above the pagination links to maintain visual balance.

### 3.31 Inventory Management Onboarding (Warehouses & Suppliers)
- **What:** Foundation for an integrated inventory system, allowing administrators to manage storage locations (Warehouses) and external vendors (Suppliers).
- **How it Works:**
  - **Warehouse Management:** Admins create and manage physical storage locations. Each warehouse record stores a unique name, a detailed physical location, and an `is_quarantine` flag.
  - **Quarantine Warehouse:** A specialized warehouse (flagged `is_quarantine: true`) is used to automatically store damaged items received during the Purchase Order process.
  - **Supplier Onboarding:** Admins manage the vendor database. Each supplier record includes the company name, contact email, mobile number, and full physical address.
- **Implementation Details:**
  - **Architecture:** Follows the strict Service Layer pattern. `InventoryService` centralizes all CRUD operations for both Warehouses and Suppliers.
  - **Validation:** `WarehouseRequest` and `SupplierRequest` enforce strict data integrity.
  - **Search & Filtering:** Both index pages utilize `FlexSearch` for real-time searching and AJAX-driven sorting.

### 3.32 Purchase Order (PO) Module & Refinement
- **What:** A comprehensive procurement system for managing product intake, refined with warehouse targeting, batch tracking, and individual serial number management.
- **How it Works:**
  - **Warehouse Targeting:** Every Purchase Order is assigned a target Warehouse upon creation. This dictates where the inventory will be physically stored once received.
  - **Itemization:** Admins add products or variants with specific `order_quantity` and `unit_cost`.
  - **Refined Receiving Workflow:** When an order is "Sent", the receiving process facilitates granular tracking:
    - **Global Batch Management:** A single `batch_number` is assigned to the entire receipt. This creates a `Batch` header record (containing the `supplier_id`) that groups all items received in that shipment.
    - **Batch Items:** Individual quantities for each product/variant are stored in the `batch_items` table, linked to the main `Batch` header.
    - **Serial Number Tagging:** The system uses a "tag-style" UI (Select2) for entering individual product serial numbers. These serials are stored in a dedicated `batch_serials` table with a status of `in-stock` by default (transitioning to `sold` upon sale).
    - **Damaged Goods Handling:** Admins specify `Received` and `Damaged` quantities. `Received` items move to the PO's target warehouse, while `Damaged` items are automatically routed to the **Quarantine** warehouse under a separate "Damaged" batch header.
  - **Automated Inventory Synchronization:**
    - **Batch-Level Tracking:** The system creates unique records in the `inventory_levels` table for each batch received. This allows for precise tracking of exactly how much of a specific batch is remaining in a warehouse.
    - **Stock & Ledger:** Updates total product stock, warehouse-batch inventory levels (`inventory_levels`), and creates detailed batch item records. Every movement triggers a **Stock Ledger** entry containing financial data (`supplier_id`, `unit_cost`, and total `cost`).
- **Implementation Details:**
  - **`PurchaseOrderService`:** Manages the complex multi-table transaction for receiving, which includes creating a `Batch` header, multiple `BatchItem` and `BatchSerial` records, updating `InventoryLevel`, and logging to the `StockLedger`.
  - **Schema Optimization:** Removed `serial_numbers` from the `purchase_order_items` table to eliminate data redundancy, as serials are now exclusively managed via `batch_serials`.
  - **Validation:** `PurchaseOrderReceiveRequest` ensures a single global `batch_number` is provided and that serial counts match the total quantities per item.

### 3.33 Stock Ledger & Audit Trail
- **What:** A centralized, immutable transaction log that tracks every stock movement (increase or decrease) across the entire system, including financial impact.
- **How it Works:**
  - **Automated Logging:** Any action that modifies stock (PO Receipt, Sales, Returns, Adjustments) automatically triggers a ledger entry.
  - **Granular Data:** Each entry records the product, variant, warehouse, batch, supplier, change quantity (positive or negative), and financial details.
  - **Financial Logic:**
    - **`unit_cost`:** This field always stores the **original purchase unit cost** of the product, regardless of the transaction type.
    - **`cost`:** This field stores the total financial value of the specific movement.
      - For **Purchases**, it records `quantity * unit_cost`.
      - For **Sales**, it records `quantity * product_price` (selling price).
      - This value can be positive (Stock In) or negative (Stock Out).
  - **Consistency:** The ledger acts as the source of truth, ensuring that the current stock levels in the `products` and `inventory_levels` tables can be reconciled against the history of transactions and their financial values.
- **Implementation Details:**
  - **`InventoryService::logStockChange()`:** A centralized method used by all modules to ensure standardized ledger recording with support for financial auditing.
  - **Database:** Uses UUIDs for primary keys to support distributed systems or future scalability.

### 3.34 Advanced Inventory Tracking (Inventory Levels)
- **What:** Granular, batch-aware tracking of products within specific warehouses, including proactive stock management features.
- **How it Works:**
  - **Batch Integration:** Stock is no longer just tracked by warehouse; it is tracked by **Warehouse + Batch**. The `inventory_levels` table stores `current_quantity` for every unique batch-warehouse-product combination.
  - **Total Stock Synchronization:** The system maintains a "Saleable Stock" model.
    - **Global Stock:** The `stock` field in the `products` and `product_variants` tables represents the **Total Saleable System Stock** (the sum of all quantities across non-quarantine warehouses).
    - **PO Receipt:** When a shipment is received, it increments the global `stock` only by the **Good/Received Quantity**. Damaged items are tracked in `inventory_levels` (Quarantine) and the Stock Ledger but are excluded from the global `stock` availability to prevent them from being sold.
    - **Sales:** When an item is sold, it decrements the global `stock`.
    - **Consistency:** This tracking ensures that the customer-facing catalog only shows items available for purchase, while the administrative reporting module provides visibility into both saleable and quarantined physical units.
  - **Customizable Thresholds:** Supports `min_stock_override` per record, allowing for fine-tuned stock alerts that override global product limits.
  - **Alert Management:** Tracks `last_alert_sent` to prevent notification spam when stock levels fall below thresholds.
- **Implementation Details:**
  - **Service Logic:** Inventory lookups now incorporate the `batch_id` to ensure accurate fulfillment.
  - **Unallocated Calculation:** In the **Stock Allocation** module, the "available to move" quantity is dynamically calculated as: `Global Stock - Sum(All Warehouse Inventory)`.

### 3.35 Inventory Reports (Stock & Batches)
- **What:** A comprehensive suite of analytical views providing real-time visibility into physical stock distribution and procurement history.
- **How it Works:**
  - **Stock Report:** A centralized view showing exactly where every physical unit is stored. It lists products, variants, warehouses, and batches along with their `current_quantity`.
    - **Drill-Down:** Product names link directly to the **Admin Product Details** page for rapid editing or analysis.
    - **Low Stock Highlighting:** Quantities that fall below their specific `min_stock_override` are visually highlighted in red.
  - **Batch Tracking:** A chronological history of all received shipments (Batches).
    - **Batch Details:** Clicking a batch reveals its header info (PO, Warehouse, Date) and a detailed table of all products received in that specific receipt.
    - **Serial Visibility:** If a batch includes serialized products, all individual serial numbers are displayed with their current status (e.g., Available, Damaged).
- **Implementation Details:**
  - **`InventoryReportController`:** Manages the specialized reporting routes and AJAX-based filtering.
  - **High-Performance Filtering:** Both reports support real-time searching by product/batch and filtering by Warehouse, utilizing `FlexSearch` for speed.
  - **UI/UX:** Uses AJAX-driven tables with URL synchronization, allowing admins to bookmark specific filtered reports.

### 3.36 Inventory Allocation Module
- **What:** A system to manage the transition of received products from a general unallocated pool to specific physical storage locations (Warehouses).
- **How it Works:**
  - **Unallocated Pool Tracking:** When a Purchase Order is received, stock is initially placed in a "virtual" unallocated pool (represented by the `stock` field in `products` or `product_variants`).
  - **Allocation Process:** Admins view a list of all items with unallocated stock. They can then select a target Warehouse and specify a quantity to move.
  - **Stock Movement:** The system decrements the unallocated stock and creates or updates a record in the `inventory_levels` table for the specific warehouse.
  - **Data Integrity:** All movements are handled within a `DB::transaction` to ensure consistency between the unallocated pool and warehouse levels.
- **Implementation Details:**
  - **`InventoryService`:** Manages the logic for retrieving unallocated items and executing the allocation transaction.
  - **`InventoryLevel` Model:** Maps products and variants to specific warehouses with their respective quantities.
  - **Security:** Access is restricted via the `inventory.allocate` permission.
  - **UI/UX:** Features a dedicated "Stock Allocation" list view and a streamlined allocation form with real-time stock availability validation.

---
*Note: This documentation is the source of truth for the smart-ecom project and is updated as the project evolves.*
