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
- **Tables & Pagination:** Use responsive Bootstrap tables with consistent styling for actions. 
    - **Pagination Info:** All paginated lists MUST include "Showing X to Y of Z Results" text next to the pagination links.
    - **Structure:** Use a `d-flex align-items-center justify-content-between` container for pagination components.
    - **Example Implementation:**
      ```blade
      <div class="mt-3">
          <div class="d-flex align-items-center justify-content-between">
              <div class="text-muted small">
                  Showing <span class="fw-semibold">{{ $data->firstItem() ?? 0 }}</span> to <span class="fw-semibold">{{ $data->lastItem() ?? 0 }}</span> of <span class="fw-semibold">{{ $data->total() }}</span> Results
              </div>
              <div>
                  {{ $data->appends(request()->all())->links() }}
              </div>
          </div>
      </div>
      ```
## 5. Performance & Flickering Prevention (STRICT)
- **Stationary Backgrounds:** Heavy CSS effects (e.g., complex radial gradients, large blurs) MUST be applied to a `fixed` pseudo-element (e.g., `.content-page::before`) rather than the scrollable content container. This prevents GPU-heavy repaints during scrolling, especially in browsers like Firefox.
- **Stable AJAX Loading:** When updating tables via AJAX, DO NOT use logic that shifts the page layout (e.g., dynamic `min-height` or aggressive `opacity` changes on the whole container). 
    - **Standard:** Use a `loadingOverlay` that covers the table content without changing its dimensions.
- **Browser Compatibility:** Avoid using `transform: translateZ(0)` or `will-change` hints on the main content containers unless absolutely necessary for specific animations, as these can cause text blurring in Firefox when combined with fixed backgrounds.
- **Mandatory Pagination:** ALL index/listing pages MUST implement Laravel's pagination (`links()`) to keep the DOM size manageable and maintain scroll performance.
