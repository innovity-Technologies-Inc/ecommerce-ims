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
    - **Testing Policy:** Automated tests (PHPUnit) are now optional and can be skipped for every module unless explicitly requested.
    - **Optimization (MANDATORY):** ALWAYS run `php artisan optimize` after completing a requirement to ensure the configuration and routes are correctly cached.
    - **Session Notifications (STRICT):** ALWAYS use the following array structure for redirect session messages (Success, Error, Warning, etc.):
      ```php
      return redirect()->route('...')->with([
          'message' => 'Your message here.',
          'alert-type' => 'success' // or 'error', 'warning', 'info'
      ]);
      ```
      NEVER use standalone `->with('success', ...)` or `->with('error', ...)` calls.
5. **Documentation Update (STRICT & MANDATORY):**
    - You MUST update `PROJECT_DOCUMENTATION.md` after completing **EVERY SINGLE INSTRUCTION** or modification.
    - **User Guide:** You MUST also update `USER_GUIDE.md` if the change affects the operational flow or UI for the end user, ensuring the non-technical guide remains accurate.
    - Reflect the new module, connections, and system flow immediately.
    - **SOP: Documentation Standard (MANDATORY):** Every module or feature update must follow this specific structure:
        - **What (Business Purpose):** High-level explanation of why the feature exists and what value it provides to the user/admin.
        - **How it Works (Technical Flow):** A step-by-step procedure of the logic (e.g., from Request -> Service Logic -> Result). Explain the "lifecycle" of the data.
        - **Data & Storage (DB Connectivity):** List related tables and explain HOW they are connected (e.g., "Table A connects to Table B via `id` to track X").
    - A task is ONLY complete when the documentation is updated with these technical specifics.
6. **Source Control (MANDATORY):**
    - After completing all the above steps and verifying the task, you MUST stage and commit all changes.
    - **Commit Message Standard:** Use a clear, descriptive message starting with a type (e.g., `feat:`, `fix:`, `docs:`) and include the requirement/task ID (e.g., `feat: add coupon modal (REQ-153)`).

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

### **Search & Filtering (Mandatory FlexSearch v4.0.0)**
- **MANDATORY:** Every search and filtering operation in the Admin Panel **MUST** utilize the `FlexSearch` engine (powered by `daiyanmozumder/laravel-flexsearch` v4.0.0+).
- **ALL** search logic and multi-column filtering parameters **MUST** be implemented within the **Service Layer** by injecting `DaiyanMozumder\FlexSearch\FlexSearch` and using its `apply()` method.
- **Service Implementation Pattern:**
  ```php
  public function listItems(array $params): LengthAwarePaginator
  {
      $query = Model::query();
      $searchConfig = [
          'searchable' => ['name', 'sku', 'description'], // Columns for keyword search
          'filterable' => ['category_id', 'status'],      // Exact match filters
          'sortable'   => ['created_at', 'price'],        // Allowed sort columns
          'relationships' => [                            // Optional: Search in relations
              'category' => ['name'],
          ]
      ];

      return $this->flexSearch->apply($query, $params, $searchConfig)
          ->paginate($params['per_page'] ?? 10);
  }
  ```
- Controllers **MUST NOT** build search queries using `->where('name', 'like', ...)` or similar manual logic.
- AJAX-driven live searching and sorting with URL synchronization (`window.history.pushState`) is the project standard.

### **Frontend Actions (Mandatory Standards)**

- **MANDATORY:** All delete buttons **MUST** use the `confirmDelete` class.
- The system uses a global SweetAlert2 handler for all elements with the `confirmDelete` class to provide a consistent deletion experience.
- Example: `<button type="submit" class="btn btn-soft-danger btn-sm confirmDelete">...</button>`

### **Data Export Standards (Excel/CSV)**

- **MANDATORY:** All data exported to Excel or CSV **MUST** be normalized to prevent blank cells.
- **Null Handling:** Use the null-coalescing operator (`??`) to provide fallbacks for all fields (`?? 0` for numeric, `?? 'N/A'` for text).
- **Forced Numeric Display:** Numeric values **MUST** be converted to strings using `number_format($value, $decimals, '.', '')` in the Controller/Service before being passed to the Export class. This ensures Excel displays `0` or `0.00` correctly instead of an empty cell.
- **Export Class Normalization:** Export classes **MUST** implement a final pass in the `array()` method to convert any remaining `null`, `false`, or empty strings to a literal string `'0'`.
- Example (Controller): `number_format($row['qty'] ?? 0, 0, '.', '')`
- Example (Export Class): `($value === null || $value === '' || $value === false) ? '0' : $value`

### **Frontend JavaScript & Scripts (STRICT)**
- **Script Sections:** Always use `@section('scripts')` for including JavaScript in Blade views. Do NOT use `@push('scripts')` as the master layout utilizes `@yield('scripts')`.
- **Blade Section Integrity (STRICT):** Every `@section` MUST have exactly one corresponding `@endsection`. When performing tool-assisted search-and-replace on Blade files, you MUST verify that redundant `@endsection` directives or trailing garbage characters are not accidentally appended to the end of the file.
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
- **Routing (STRICT):**
    - Use named routes for all links.
    - **Route Verification (MANDATORY):** Before adding or changing a route in a Blade file, you MUST verify its existence using `php artisan route:list --name=your.route.name`.
    - **No Fictional Routes:** NEVER guess or create fictional route names (e.g., using `.show` when only `.read` exists). If a route for similar functionality already exists, use it; otherwise, request a new route definition.
- **Models:**
    - Use `casts()` method instead of `$casts` property.
    - Define relationships with explicit return types (e.g., `: HasMany`).
    - **MANDATORY:** When adding new columns to a database table via migration, you MUST always add the corresponding field to the `$fillable` array in the Eloquent model to prevent mass-assignment issues.
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
- **Currency Symbols (MANDATORY):** 
    - NEVER hardcode currency symbols (e.g., "$") in Blade views, emails, or reports.
    - ALWAYS use the dynamic symbol from general settings: `{{ \App\HelperClass::generalSettings()->currency ?? '$' }}`.
    - If a `$gs` variable is already defined in the view (via `HelperClass::generalSettings()`), use `{{ $gs->currency ?? '$' }}`.

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
7. **Formatting:** Run `./vendor/bin/pint --dirty` before finalizing.
8. **Optimization:** Run `php artisan optimize` to refresh configuration, route, and view caches.
9. **Commit:** Stage and commit all changes with a task-referenced message.

