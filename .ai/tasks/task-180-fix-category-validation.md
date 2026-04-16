# Task: 180 - Fix Category Validation and Slug Generation

## Requirement
Prevent duplicate category names within the same parent while allowing them across different parents. Ensure globally unique slugs.

## Objectives
1.  **Scoped Validation**: Update `CategoryRequest` to validate name uniqueness scoped to `parent_id`.
2.  **Unique Slugs**: Implement a service-level slug generator that handles collisions by prepending parent names or adding suffixes.

## Implementation Steps

### 1. Update Validation (REQ-180)
- Update `app/Http/Requests/Admin/CategoryRequest.php` to use a scoped unique rule for the `name` field.

### 2. Update Slug Logic (REQ-181)
- Update `app/Services/CategoryService.php` to include `generateUniqueSlug()`.
- Refactor `storeCategory` and `updateCategory` to use this new method.

## Verification Criteria
- [x] Adding "Shoe" under "Men" works.
- [x] Adding "Shoe" under "Women" works (slug becomes `women-shoe`).
- [x] Adding "Shoe" again under "Men" fails with a validation error.
- [x] Updating a category without changing its name doesn't trigger a unique error.
