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
- **Admin Panel:** Always extend `@extends('admin.structure.master')`.
- **Client Frontend:** Always extend `@extends('client.structure.master')`.
- **Modals:** Use Bootstrap 5 modal components.
- **Responsive Design:** Ensure all components are mobile-first and fully responsive across breakpoints (xs, sm, md, lg, xl, xxl).

## 4. UI/UX Principles
- **Visual Feedback:** Provide immediate feedback for user actions (e.g., loading spinners on buttons, Toastr for success/error).
- **Consistency:** Use consistent spacing, typography (sans-serif), and color palettes across all views.
- **Forms:** Labels should always be present; use placeholders appropriately. Use Select2 for all searchable/multi-select dropdowns.
- **Tables:** Use responsive Bootstrap tables with consistent styling for actions (icons for edit/delete).
