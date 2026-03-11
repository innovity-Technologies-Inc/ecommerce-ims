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
5. **Documentation Update (STRICT & DETAILED):**
    - Update `PROJECT_DOCUMENTATION.md` to reflect the new module, connections, and system flow.
    - **Detail Requirement:** Every update must include a "What" (Business purpose) and "How it Works / Implementation Details" (Technical flow, Service logic, DB interactions).
    - A task is ONLY complete when the documentation is updated with these technical specifics.

## 2. Architectural Patterns (MANDATORY & ENFORCED)

### **Service Layer & Form Request Pattern (STRICT)**
- It is **MANDATORY** for every Controller to use a dedicated **Form Request** class for validation and a **Service** class for logic.
- **ALL** business logic, data calculations, and database operations (queries, updates, deletions) **MUST** reside in Service classes (`app/Services`).
- **NO** logic is allowed in Controllers. They are strictly for receiving validated data and returning responses.

### **Thin Controllers (Routing Only)**
- Controllers **MUST** be thin. Their sole responsibility is to:
    1. Receive a **Form Request** (automatically validated).
    2. Pass the validated data to a **Service**.
    3. Return a view, redirect, or JSON response based on the Service's result.
- **NEVER** use `Model::query()`, `DB::table()`, or any direct data manipulation inside a Controller.

### **Validation (Mandatory Form Requests)**
- Every store, update, or action-based request **MUST** have a dedicated class created via `php artisan make:request`.
- Inline `$request->validate([...])` is strictly prohibited.

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
- **File Uploads:** `HelperClass::file_upload($file, $folder)`.
- **File Deletions:** `HelperClass::file_delete($path)`.
- **Table Serialization:** `HelperClass::indexNumberSerialization($paginatedData)`.

## 5. Development Workflow
1. **Migration & Model:** Set up the DB layer.
2. **Form Request:** Define validation rules.
3. **Service:** Implement business logic and DB operations.
4. **Controller:** Route the request to the Service.
5. **Testing & Verification:**
    - **Seeder-Driven Verification:** ALWAYS use existing Seeders to populate test data. DO NOT create factories.
    - Write PHPUnit tests in `tests/Feature` or verify manually using seeded data.
6. **Formatting:** Run `./vendor/bin/pint --dirty` before finalizing.
