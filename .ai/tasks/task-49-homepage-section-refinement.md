# Task 49: Homepage Section Refinement

Implement a new "Top Picks" homepage section and ensure all homepage sections have independent visibility and content controls in the Admin Panel.

## Requirements
- **REQ-74:** Homepage Section Refinement.

## Implementation Steps

### 1. Database & Seeding
- [x] Add a new record for `top_picks` in the `section_settings` table via a migration or a seeder.
- [x] Ensure the migration handles the `section_name`, `section_title`, `mode`, etc.

### 2. Service Layer & Logic
- [x] Update `HomepageService::getSectionProducts` to handle the `top_picks` section.
- [x] Define the "Organic" logic for `top_picks` (e.g., highly rated or specific flags).

### 3. Admin Panel Integration
- [x] Update the Homepage Section Settings view in the Admin Panel to include the "Top Picks" section.
- [x] Ensure all sections (Bestsellers, Hot Deals, Featured, Recently Added, Top Picks) have independent "Is Visible" toggles and "Mode" (Organic/Custom) selection.

### 4. Client Frontend Integration
- [x] Create `resources/views/client/partials/top_picks.blade.php`.
- [x] Update `FrontendController@home` to fetch the `top_picks` section data.
- [x] Include `top_picks` in `resources/views/client/homepage.blade.php`.
- [x] Replace `feature_1` or `recent` if redundant, as per the user's request to differentiate the sections.

### 5. Verification
- [x] Verify that "Top Picks" appears in the Admin Panel.
- [x] Verify that toggling "Is Visible" correctly hides/shows sections on the homepage.
- [x] Verify that "Top Picks" pulls correct data in both Organic and Custom modes.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- A new "Top Picks" section is available and configurable in the Admin Panel.
- All homepage sections can be independently turned on or off.
- The homepage displays a unique "Top Picks" section without redundancy.
