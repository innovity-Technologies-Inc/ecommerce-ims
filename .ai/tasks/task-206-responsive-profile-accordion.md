# Task 206: Responsive Profile Layout (Mobile Accordion)

Update the profile page to behave like an accordion on mobile devices while maintaining the sidebar-tab layout on desktop.

## Requirement Reference
- **REQ-206:** Responsive Profile Layout (Mobile Accordion).

## Implementation Steps

### 1. Structure Restructuring
- **File:** `resources/views/client/auth/account_info.blade.php`
- **Changes:** 
    - Move the `tab-pane` content divs from the separate right-hand column into the sidebar container, placing each pane immediately after its corresponding navigational button.
    - Use CSS Grid on desktop (`lg` breakpoint and up) to reposition these panes into the right-hand column.

### 2. Styling (CSS Grid)
- Update the `<style>` block in `account_info.blade.php`:
    - On mobile: Panes will naturally flow after their buttons, creating an accordion-like effect.
    - On desktop (`@media (min-width: 992px)`):
        - Set the parent container to `display: grid`.
        - Define columns for sidebar and content.
        - Use `grid-column` and `grid-row` to force all active panes into the same content area (the second column).

### 3. Navigation Update
- Ensure Bootstrap's tab transitions work correctly with the new DOM order.

### 4. Verification
- **Desktop:** Verify sidebar tabs show content on the right.
- **Mobile:** Verify clicking a tab button expands the form directly below it.
- **Form Integrity:** Ensure all 3 forms (Profile, Password, Address) function correctly.

## Documentation Update
- Update `PROJECT_DOCUMENTATION.md` to note the responsive CSS Grid implementation for the profile page.
