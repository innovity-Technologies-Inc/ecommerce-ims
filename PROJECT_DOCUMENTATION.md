# smart-ecom Project Documentation

## 1. Project Overview
**smart-ecom** is a modern e-commerce platform built with Laravel 12. It features a dual-interface system: a robust **Admin Panel** for catalog and site management, and a polished **Customer Frontend** for shoppers.

### Core Tech Stack
- **Backend:** PHP 8.3.8, Laravel 12 (Streamlined Structure)
- **Frontend:** Bootstrap 5, jQuery 3.6, standard JavaScript
- **Database:** MySQL / PostgreSQL (Eloquent ORM)
- **Authentication:** Laravel Breeze (Multi-auth: Admin & User)
- **Tooling:** Vite (Bundling), Laravel Pint (Linting), PHPUnit 11 (Testing)

---

## 2. Architecture & Design Patterns

### Service Layer Pattern
To keep controllers thin and logic reusable, complex business operations (like Product creation with variants and images) are handled in the `app/Services` directory.
- **Example:** `ProductService.php` manages the lifecycle of products, ensuring data integrity across multiple tables without relying solely on database foreign keys.

### Centralized Utility (`App\HelperClass`)
All common operations are abstracted into a static `HelperClass`. This is a strict requirement for:
- **Index Serialization:** `HelperClass::indexNumberSerialization($data)` for consistent table row numbering.
- **File Management:** `HelperClass::file_upload()` and `HelperClass::file_delete()` for unified storage logic.
- **Global Settings:** `HelperClass::generalSettings()` for accessing site-wide configurations.

### Directory Structure
- `app/Http/Controllers/`: Separated into `Auth`, `Admin`, and `Frontend` logic.
- `app/Http/Requests/`: Form Requests for strict validation (e.g., `ProductRequest.php`).
- `app/Models/`: Eloquent models with defined relationships and casts.
- `resources/views/admin/`: Admin panel views using the Admin Master template.
- `resources/views/client/`: Customer frontend views using the Client Master template.

---

## 3. Development Workflow
When adding a new module (e.g., "Coupon System"), follow this flow:

1.  **Database:** Create migration using `php artisan make:migration`.
2.  **Model:** Create Model with `php artisan make:model`, define `$fillable` and relationships.
3.  **Validation:** Create Form Request using `php artisan make:request`.
4.  **Logic:** If complex, add methods to a Service class.
5.  **Controller:** Create Controller, inject Services, and return views.
6.  **UI:** Implement Blade views using the established Bootstrap templates.
7.  **Testing:** Write a Feature Test in `tests/Feature` to verify the module.
8.  **Formatting:** Run `./vendor/bin/pint` to ensure code style compliance.

---

## 4. Frontend & Templating Standards

### Base Layouts
- **Admin:** `@extends('admin.structure.master')`
- **Client:** `@extends('client.structure.master')`

### UI Libraries & Plugins
The project strictly avoids Tailwind/Alpine.js in favor of:
- **Styling:** Bootstrap 5 (Customized via CSS variables).
- **Interactions:** jQuery (AJAX, DOM manipulation).
- **Modals/Alerts:** SweetAlert2.
- **Notifications:** Toastr.
- **Selects:** Select2 (Bootstrap 5 theme).
- **Editors:** Summernote.
- **File Uploads:** FilePond (with Image Preview/Resize/Transform plugins).

---

### Coding Standards
- **Thin Controllers:** Controllers only route requests and return responses.
- **Form Requests:** Strict use of **Form Request Classes** for all validation logic.
- **Service Layer Pattern:** ALL business logic and data persistence MUST reside in Services (`app/Services`).
- **PHP 8+ Features:** Use constructor property promotion and explicit return type hints.
- **Formatting:** Strict adherence to Laravel Pint (PSR-12/Laravel style).
- **Control Structures:** Always use curly braces `{}`.
- **Naming:** CamelCase for variables/methods, TitleCase for Enums.

---

## 6. Current Modules
- **Authentication:** Multi-guard setup for Admins and Users.
- **Catalog:** Brands, Categories (Parent/Child), and Products.
- **Inventory:** Product Variants (Size, Color, SKU, Price, Stock).
- **Product Flags:** Specific boolean markers for marketing and filtering:
    - `is_new_arrival`: Newly Arrival
    - `is_hot_deal`: Hot Deals
    - `is_featured`: Featured
- **Media:** Product Gallery management with primary image logic.
- **Settings:** Centralized site configuration management:
    - **General Settings:** Business Name, Logos (Dark/Light), Favicon, Breadcrumb Image, Currency, and global SEO Meta data.
    - **Mail Settings:** Dynamic SMTP configuration (Host, Port, Username, Password, Encryption, and From Address/Name) persisted in DB and loaded at runtime.
- **Client Product Module:**
    - **Dynamic Shop Page:** Replaced static templates with Eloquent loops for grid and list views.
    - **Global Navbar Search:** Fully integrated navbar search (desktop/mobile) with category targeting.
    - **Advanced Filtering:** Sidebar filters for Category, Brand, Size, and Color.
    - **Price Range Slider:** Custom dynamic price slider (0 to Max) with automatic form submission.
    - **FlexSearch Integration:** Powered by `daiyanmozumder/laravel-flexsearch` for keyword and relationship searching across name, description, brand, and category.
- **Wishlist Module:**
    - **Persistent Wishlist:** Authenticated users can save products to their wishlist.
    - **Reusable Components:** Uses a unified `cart_view.blade.php` partial for both Wishlist and Shopping Cart displays.
    - **Accurate Pricing:** Automatically displays the lowest variant price for wishlisted items.

- **Flexible Pricing Model:** 
    - Supports two pricing modes selected via radio buttons: **Base Pricing** (one price for all variants) and **Variant Pricing** (unique price per variant).
    - **Optional Variants:** Products can now be created with only base details and no variants.
    - **Calculated Prices:** Discounted prices are automatically calculated by the `ProductService` if a discount percentage is provided.
    - **Logic for Cart/Orders:**
        - Always check for **Variant Pricing** first: If a variant has a `regular_price`, use it (and its corresponding `discount_price`).
        - Fallback to **Product Base Pricing**: If the variant's `regular_price` is null, use the parent product's `regular_price` and `discount_price`.
        - **Net Price:** The final selling price must always be the `discount_price` if present, otherwise the `regular_price`.

## 7. Recently Fixed Issues
- **Product Edit (Subcategory Selection):** Resolved an issue where subcategories were not pre-selected when editing a product. Fixed by improving the Select2 initialization and category change logic in the Blade view.
- **Product Update (SKU Validation):** Fixed a bug where updating a product would fail due to SKU uniqueness constraints (even if the SKUs belonged to the product being updated). The `ProductRequest` now ignores existing SKUs for the current product during the update process.
- **Admin UI:** Cleaned up redundant jQuery inclusions and standardized script placement within the `@section('scripts')` block.

---
*Note: This document is updated automatically whenever new modules or architectural changes are implemented.*
