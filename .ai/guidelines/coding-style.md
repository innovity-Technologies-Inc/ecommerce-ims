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
    - Verify with PHPUnit tests before finalization.
5. **Documentation Update (STRICT):**
    - Update `PROJECT_DOCUMENTATION.md` to reflect the new module, connections, and system flow.
    - A task is ONLY complete when the documentation is updated.

## 2. Architectural Patterns
### **Service Layer Pattern (STRICT)**
- **All** business logic, data calculations, and database operations MUST reside in Service classes (`app/Services`).
- Services are injected into controllers using constructor property promotion.
- Methods in Services should be focused, reusable, and have clear return types.

### **Thin Controllers**
- Controllers are responsible for **routing only**.
- They receive validated data from **Form Requests**, pass it to a Service, and return a view or a redirect.
- Avoid all DB queries (`Model::find()`, etc.) and logic within controllers.

### **Validation (Form Requests)**
- Use `php artisan make:request` for all input validation.
- All validation rules MUST be defined in these dedicated classes.

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
5. **Testing:** Write PHPUnit tests in `tests/Feature`.
6. **Formatting:** Run `./vendor/bin/pint --dirty` before finalizing.
