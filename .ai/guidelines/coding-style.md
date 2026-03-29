# Coding Style & Architecture

The smart-ecom project follows strict architectural standards and a **mandatory development workflow** to ensure code quality, testability, and long-term maintainability.

## 1. Mandatory Development Workflow (NO EXCEPTIONS)

You MUST strictly follow this sequence for **EVERY** request:

1. **Requirement Logging (STRICT):**
    - Append the new requirement to `.ai/requirements/requirements.md` with a unique ID (e.g., REQ-XX).
2. **Task Creation (STRICT):**
    - Create a new task file in `.ai/tasks/task-xx-name.md`.
    - Detail the implementation steps and verification criteria based on the requirement.
3. **Surgical Implementation:**
    - Execute the task following the architectural patterns defined below.
    - Use PHP 8.3 features and Laravel 12 standards.
4. **Verification & Styling:**
    - Run `./vendor/bin/pint --dirty` to maintain project styling.
    - **Seeder-Driven Verification (STRICT):** Verify logic using existing Seeders (`php artisan db:seed`) to populate dummy data. DO NOT create new Model Factories.
    - Verify with PHPUnit tests before finalization.
    - **Optimization (MANDATORY):** ALWAYS run `php artisan optimize` after completing a requirement to ensure the configuration and routes are correctly cached.
5. **Documentation Update (STRICT & MANDATORY):**
    - You MUST update `PROJECT_DOCUMENTATION.md` after completing **EVERY SINGLE INSTRUCTION** or modification.
    - Reflect the new module, connections, and system flow immediately.
    - **Detail Requirement:** Every update must include a "What" (Business purpose) and "How it Works / Implementation Details" (Technical flow, Service logic, DB interactions).
    - A task is ONLY complete when the documentation is updated with these technical specifics.

## 2. Architectural Patterns (MANDATORY & ENFORCED)

### **Service Layer & Form Request Pattern (STRICT)**
- It is **MANDATORY** for every Controller to use a dedicated **Form Request** class for validation and a **Service** class for logic.
- **ALL** business logic, data calculations, and database operations (queries, updates, deletions) **MUST** reside in Service classes (`app/Services`).
- **NO** logic is allowed in Controllers. They are strictly for receiving validated data and returning responses.

### **Error Handling (MANDATORY)**

- **MANDATORY:** Every `catch` block **MUST** log the error using `Log::error($e->getMessage())` (or a more descriptive message) before returning a response or re-throwing. This is critical for debugging SQL and system errors that are otherwise hidden from the user.

### **Thin Controllers (Routing Only)**
- Controllers **MUST** be thin. Their sole responsibility is to:
    1. Receive a **Form Request** (automatically validated).
    2. Pass the validated data to a **Service**.
    3. Return a view, redirect, or JSON response based on the Service's result.
- **NEVER** use `Model::query()`, `DB::table()`, or any direct data manipulation inside a Controller.

### **Validation (Mandatory Form Requests)**
- Every store, update, or action-based request **MUST** have a dedicated class created via `php artisan make:request`.
- Inline `$request->validate([...])` is strictly prohibited.

### **Search & Filtering (Mandatory FlexSearch)**
- **MANDATORY:** Every search and filtering operation in the Admin Panel **MUST** utilize the `FlexSearch` engine (powered by `daiyanmozumder/laravel-flexsearch`).
- **ALL** search logic and multi-column filtering parameters **MUST** be implemented within the **Service Layer** by injecting `FlexSearch` and using its `apply()` method.
- Controllers **MUST NOT** build search queries using `->where('name', 'like', ...)` or similar manual logic.
- AJAX-driven live searching and sorting with URL synchronization (`window.history.pushState`) is the project standard.

### **Frontend Actions (Mandatory Standards)**
- **MANDATORY:** All delete buttons **MUST** use the `confirmDelete` class.
- The system uses a global SweetAlert2 handler for all elements with the `confirmDelete` class to provide a consistent deletion experience.
- Example: `<button type="submit" class="btn btn-soft-danger btn-sm confirmDelete">...</button>`

### **Frontend JavaScript & Scripts (STRICT)**
- **Script Sections:** Always use `@section('scripts')` for including JavaScript in Blade views. Do NOT use `@push('scripts')` as the master layout utilizes `@yield('scripts')`.
- **Dynamic Metadata:** When passing metadata from PHP/Eloquent to dynamic JavaScript (e.g., product IDs, prices), always use `data-*` attributes on HTML elements.
- **PROHIBITED:** Never generate nested PHP loops (`@foreach`) inside `<script>` tags to handle lookups. Use data attributes or JSON-encoded objects instead.
- **Event Binding:** For dynamic elements or standard buttons, prefer robust event delegation or direct binding: `$(document).on('click', '#id', ...)` or `$('#id').on('click', ...)`.

### **Security & Permissions (STRICT)**
- **Naming Convention:** Use simplified, two-part granular permissions: `module.operation` (e.g., `warehouse.view`, `supplier.create`, `po.edit`).
- **Middleware:** Every route must be protected by its specific granular permission middleware.
- **UI Protection:** All action buttons (Add, Edit, Delete) must be wrapped in `@can('permission.name')` directives.

## 2. PHP 8.3 Standards
- **Constructor Property Promotion:** Use `public function __construct(protected Service $service) {}`.
- **Explicit Typing:** Every method MUST have a defined return type hint (e.g., `: bool`, `: View`, `: RedirectResponse`).
- **Typed Properties:** All class properties must have type declarations.
- **Enums:** Use TitleCase for Enum keys.

## 3. Laravel 12 Conventions
- **Routing:** Use named routes for all links.
- **Models:**
    - Use `casts()` method instead of `$casts` property.
    - Define relationships with explicit return types (e.g., `: HasMany`).
- **Middleware/Exceptions:** Configured in `bootstrap/app.php`.
- **Configuration:** Always use `config('key')`, never `env('KEY')` outside of config files.

## 4. HelperClass Usage
- **MANDATORY:** Always use `HelperClass` for retrieving global settings (General, Contact, Brands, etc.) within Blade templates. Global view sharing via Service Providers (`View::share`) is strictly prohibited.
- **File Uploads:** `HelperClass::file_upload($file, $folder)`.
- **File Deletions:** `HelperClass::file_delete($path)`.
- **Table Row Numbering (MANDATORY):** 
    - Always use `HelperClass::indexNumberSerialization($paginatedData)` to initialize the starting serial number.
    - **Pattern:** Initialize a `$sl` variable before the loop and increment it using `{{ $sl++ }}` inside the loop.
    - **PROHIBITED:** Never use `indexNumberSerialization($data)[$loop->index]` as it causes "Trying to access array offset on int" errors.
    - **Example:**
      ```blade
      @php $sl = \App\HelperClass::indexNumberSerialization($data); @endphp
      @foreach($data as $item)
          <td>{{ $sl++ }}</td>
      @endforeach
      ```
- **Global Data Access:** Use `HelperClass::generalSettings()`, `HelperClass::contactSettings()`, `HelperClass::getCategories()`, etc.

## 6. Database Safety & Data Integrity (CRITICAL)

### **Destructive Operations (STRICT & MANDATORY)**
- **Warning Before Action:** The AI agent **MUST** explicitly warn the user before executing any command that modifies the database in a destructive way (e.g., `git reset --hard`, `php artisan migrate:fresh`, `db:wipe`, or manual `DELETE` queries).
- **Confirmation Requirement:** You **MUST** seek explicit user confirmation before deleting any record from the database, even if it was previously implied in a request.
- **Data Preservation:** During migrations (`php artisan migrate`), the agent **MUST** verify that the migration being run does not contain `dropColumn` or `dropTable` commands unless specifically requested and confirmed by the user.
- **Backup Awareness:** Before any major database structural change, the agent should suggest that the user takes a backup of their current data.
- **Error Handling:** If a database operation fails or returns a "null" result where data was expected, the agent **MUST** stop and investigate rather than proceeding with assumptions that could lead to data loss.

## 7. Development Workflow
1. **Migration & Model:** Set up the DB layer.
2. **Form Request:** Define validation rules.
3. **Service:** Implement business logic and DB operations.
4. **Controller:** Route the request to the Service.
5. **Testing & Verification:**
    - **Seeder-Driven Verification:** ALWAYS use existing Seeders to populate test data. DO NOT create factories.
    - Write PHPUnit tests in `tests/Feature` or verify manually using seeded data.
6. **Formatting:** Run `./vendor/bin/pint --dirty` before finalizing.
7. **Optimization:** Run `php artisan optimize` to refresh configuration, route, and view caches.
