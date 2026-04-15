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
