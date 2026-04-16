<laravel-boost-guidelines>
=== .ai/coding-style rules ===

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

### **Search & Filtering (Mandatory FlexSearch)**
- **MANDATORY:** Every search and filtering operation in the Admin Panel **MUST** utilize the `FlexSearch` engine (powered by `daiyanmozumder/laravel-flexsearch`).
- **ALL** search logic and multi-column filtering parameters **MUST** be implemented within the **Service Layer** by injecting `FlexSearch` and using its `apply()` method.
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
7. **Formatting:** Run `./vendor/bin/pint --dirty` before finalizing.
8. **Optimization:** Run `php artisan optimize` to refresh configuration, route, and view caches.
9. **Commit:** Stage and commit all changes with a task-referenced message.

=== .ai/design-guidelines rules ===

# Design Guidelines

This project focuses on a clean, modern, and high-performance e-commerce interface using a classic, reliable tech stack.

## 1. Core Tech Stack (MANDATORY)
- **Framework:** Bootstrap 5 (Customized via SCSS/CSS variables).
- **Interactivity:** jQuery 3.6+, standard JavaScript.
- **Alerts/Modals:** SweetAlert2.
- **Notifications:** Toastr.
- **Selects:** Select2 (Bootstrap 5 theme).
- **Rich Text:** Summernote.
- **File Uploads:** FilePond.

## 2. Prohibited Technologies
- **NO Tailwind CSS:** Do not use utility-first CSS frameworks.
- **NO Alpine.js:** All frontend interactivity must be handled via jQuery or standard JS.
- **NO React/Vue/Svelte:** This is a Blade-centric application.

## 3. Layout Structure
- **Admin Panel:** Always extend `@extends('admin.structure.app')`.
- **Client Frontend:** Always extend `@extends('client.structure.master')`.
- **Containers:** For standard Admin Panel pages, always use `<div class="container-xxl">`. Use `container-fluid` only if explicitly required for wide dashboards.
- **Modals:** Use Bootstrap 5 modal components.
- **Responsive Design:** Ensure all components are mobile-first and fully responsive across breakpoints (xs, sm, md, lg, xl, xxl).

## 4. UI/UX Principles
- **Icons:** Use `iconify-icon` with the `solar` icon set for consistency (e.g., `<iconify-icon icon="solar:eye-broken"></iconify-icon>`).
- **Action Buttons:** Standardize all primary action buttons in the Admin Panel to use **icons only** (no text). This applies to "View", "Details", "Edit", "Create/Add", and "Delete" buttons within tables and listing pages.
    - **View/Details:** Eye icon (`solar:eye-broken` or `solar:eye-bold-duotone`).
    - **Edit:** Pen icon (`solar:pen-2-broken` or `solar:pen-new-square-bold-duotone`).
    - **Delete:** Trash icon (`solar:trash-bin-trash-broken` or `solar:trash-bin-trash-bold-duotone`).
    - **Create/Add:** Plus icon (`solar:add-circle-bold-duotone`).
- **Visual Feedback:** Provide immediate feedback for user actions (e.g., loading spinners on buttons, Toastr for success/error).
- **Forms:** Labels should always be present; use placeholders appropriately. Use Select2 for all searchable/multi-select dropdowns.
- **Tables & Pagination:** Use responsive Bootstrap tables with consistent styling for actions. All paginated lists MUST include "Showing X to Y of Z Results" text next to the pagination links.
- **Consistency:** Use consistent spacing, typography (sans-serif), and color palettes across all views.

=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.8
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/socialite (SOCIALITE) - v5
- laravel/breeze (BREEZE) - v2
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11
- alpinejs (ALPINEJS) - v3
- tailwindcss (TAILWINDCSS) - v3

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domainâ€”don't wait until you're stuck.

- `tailwindcss-development` â€” Styles applications using Tailwind CSS v3 utilities. Activates when adding styles, restyling components, working with gradients, spacing, layout, flex, grid, responsive design, dark mode, colors, typography, or borders; or when the user mentions CSS, styling, classes, Tailwind, restyle, hero section, cards, buttons, or any visual/UI changes.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging

- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== pint/core rules ===

# Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).

=== tailwindcss/core rules ===

# Tailwind CSS

- Always use existing Tailwind conventions; check project patterns before adding new ones.
- IMPORTANT: Always use `search-docs` tool for version-specific Tailwind CSS documentation and updated code examples. Never rely on training data.
- IMPORTANT: Activate `tailwindcss-development` every time you're working with a Tailwind CSS or styling-related task.
</laravel-boost-guidelines>
