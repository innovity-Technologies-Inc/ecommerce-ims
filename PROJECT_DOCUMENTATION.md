# smart-ecom Project Documentation

## 1. Project Overview
**smart-ecom** is a high-performance, modern e-commerce platform built with **Laravel 12**. It uses a dual-interface architecture: a comprehensive **Admin Panel** for business operations and a sleek, responsive **Customer Frontend** for shoppers.

### Core Tech Stack
- **Backend:** PHP 8.3.8, Laravel 12 (Streamlined Structure)
- **Frontend:** Bootstrap 5, jQuery 3.6, standard JavaScript
- **Database:** MySQL / PostgreSQL (Eloquent ORM)
- **Authentication:** Laravel Breeze (Multi-auth: Admin & User guards)
- **Search:** FlexSearch (powered by `daiyanmozumder/laravel-flexsearch`)
- **AI Tools:** Custom guidelines and tasks in `.ai/` and Laravel Boost/Junie.

---

## 2. Project & Folder Structure

### Key Directories
- `app/Http/Controllers/`: Thin controllers separated into `Admin/`, `Auth/`, and `Frontend`.
- `app/Http/Requests/`: Strict **Form Request** classes for all input validation.
- `app/Services/`: **MANDATORY** Service Layer for all business logic and DB operations.
- `app/Models/`: Eloquent models with defined relationships and `casts()`.
- `app/HelperClass.php`: Centralized static methods for file uploads, deletions, and indexing.
- `.ai/`: AI-specific configurations, guidelines, tasks, and requirements.
- `resources/views/admin/`: Admin panel views (Master layout: `admin.structure.master`).
- `resources/views/client/`: Customer frontend views (Master layout: `client.structure.master`).

### AI Folder Structure (`.ai/`)
- `.ai/guidelines/`: Project-specific standards for design and coding.
- `.ai/requirements/`: Feature specifications and requirements docs.
- `.ai/skills/`: AI specialized knowledge (e.g., `laravel-developer.md`).
- `.ai/tasks/`: Specific development tasks and workflows.

---

## 3. Module Connections & System Flow

### Request Lifecycle (The "Laravel Boost" Flow)
1. **Route:** User makes a request to a named route.
2. **Form Request:** Inputs are automatically validated (e.g., `ProductRequest`).
3. **Controller:** Receives validated data, injects a **Service**, and calls a method.
4. **Service:** Executes business logic, handles file uploads via `HelperClass`, and interacts with **Models**.
5. **Model:** Performs DB operations and returns Eloquent collections.
6. **Response:** Controller returns a view or a JSON response.

### Core Module Connections
- **Catalog System:** 
    - `Brand` -> `Category` (Parent/Child) -> `Product`.
    - `Product` has many `ProductVariant` (Pricing/Stock) and `ProductImage` (Gallery).
- **Frontend Shop:** 
    - Uses `FlexSearch` for keywords and relationships (Name, Brand, Category).
    - Sidebar filters interact with `Product` attributes (Brand, Category, Size, Color, Price).
- **Settings System:**
    - `GeneralSetting`: Site-wide SEO, Logos, and Currency.
    - `MailSetting`: Dynamic SMTP configuration loaded via Middleware or at runtime.
    - `SectionSetting`: Homepage visibility and "Bestseller" logic (Organic vs. Custom).
- **Homepage Management:**
    - `Slider`: Carousel management.
    - `SectionSetting`: Controls visibility and content source for homepage sections.

---

## 4. Architectural Standards

### Service Layer Pattern
To ensure maintainability, controllers MUST NOT contain logic. 
- **Incorrect:** `Product::create($request->all());` in Controller.
- **Correct:** `$this->productService->createProduct($request->validated());` in Controller.

### HelperClass Usage
Strict adherence to `App\HelperClass` for:
- **File Management:** `HelperClass::file_upload()` and `HelperClass::file_delete()`.
- **Indexing:** `HelperClass::indexNumberSerialization($paginatedData)`.

### Frontend Requirements
- **Bootstrap 5 & jQuery ONLY.**
- **NO Tailwind CSS or Alpine.js.**
- Use **SweetAlert2** for confirmations and **Toastr** for notifications.
- Use **Select2** for all searchable dropdowns.

---

## 5. Development Guidelines (Synced from `.ai/guidelines`)

### Coding Style
- **PHP 8.3:** Use constructor property promotion and explicit return type hints.
- **Laravel 12:** Use `casts()` method on models; define relationship return types.
- **Testing:** Every new feature requires a PHPUnit test in `tests/Feature`.
- **Formatting:** Run `./vendor/bin/pint --dirty` before every commit.

### Design Principles
- **Mobile-First:** Ensure all components are responsive.
- **Visual Feedback:** All asynchronous (AJAX) actions must show loading states.
- **Consistency:** Follow existing spacing, typography, and color schemes.

---

## 6. Current Modules & Features
- **Multi-Guard Auth:** Separate logic for Admin and Customer sessions.
- **Product Flexible Pricing:** Support for **Base Pricing** and **Variant Pricing**.
- **Inventory Management:** SKU-level stock tracking for variants.
- **Dynamic Shop Sidebar:** Filters for Categories, Brands, Sizes, Colors, and Price Range.
- **Global Search:** FlexSearch integration across multiple tables.
- **Wishlist:** Authenticated persistent wishlist with dynamic pricing logic.
- **Hybrid Cart System:**
    - Uses **Database** for authenticated users and **Sessions** for guest users.
    - Synchronizes guest cart items to the database automatically upon login or registration.
    - Dynamic AJAX updates for cart count, totals, and mini-cart in the header.
    - Full cart management page with **8/4 grid split** for promotional banner and grand totals.
    - **UI Alignment:** Flexbox `align-items-stretch` used to match promotional banner height with the Cart Total card.
    - Mobile-optimized **40/60 Grid layout** for product items (Centered Image / Stacked Details).
- **Mobile Header Refinements:**
    - Clean **3-6-3 Column Grid** for Menu, Logo, and Action Icons.
    - Optimized logo centering and action button spacing for small devices.
- **Search Component Refinements:**
    - Streamlined global search by removing the category dropdown, ensuring a cleaner UI and focused search experience across all devices.
- **SMTP Settings:**
 Dynamic DB-driven mail configuration.
- **Homepage Sections:** Configurable sections (Bestsellers, Sliders, Featured).

---
*Note: This documentation is the source of truth for the smart-ecom project and is updated as the project evolves.*
