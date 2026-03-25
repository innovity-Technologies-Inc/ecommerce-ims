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
- **Visual Feedback:** Provide immediate feedback for user actions (e.g., loading spinners on buttons, Toastr for success/error).
- **Forms:** Labels should always be present; use placeholders appropriately. Use Select2 for all searchable/multi-select dropdowns.
- **Tables & Pagination:** Use responsive Bootstrap tables with consistent styling for actions. All paginated lists MUST include "Showing X to Y of Z Results" text next to the pagination links.
- **Consistency:** Use consistent spacing, typography (sans-serif), and color palettes across all views.
