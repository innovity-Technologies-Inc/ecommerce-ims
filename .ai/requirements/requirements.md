# smart-ecom Requirements Overview

This document lists the high-level requirements for the modules implemented in the smart-ecom project.

## 1. Authentication & Security
- [x] **REQ-01:** Admin Login & Dashboard Access.
- [x] **REQ-02:** Client (Customer) Registration & Login.
- [x] **REQ-03:** Multi-Guard Session Management (Admin vs. User).
- [x] **REQ-04:** Profile Management for both Admins and Customers.

## 2. Catalog Management
- [x] **REQ-05:** Brand CRUD (Logo, Slug, Status).
- [x] **REQ-06:** Category & Subcategory Management (Parent/Child Hierarchy).
- [x] **REQ-07:** Slug Generation & Image Handling for Catalog Items.

## 3. Product & Inventory System
- [x] **REQ-08:** Product CRUD with Marketing Flags (New Arrival, Hot Deal, Featured).
- [x] **REQ-09:** Flexible Pricing Engine (Base Pricing vs. Variant Pricing).
- [x] **REQ-10:** Product Variant Management (Variant Name, SKU, Stock).
- [x] **REQ-11:** Multi-Image Gallery with Primary Image selection.

## 4. Customer Shop Frontend
- [x] **REQ-12:** Dynamic Product Listing (Grid & List views).
- [x] **REQ-13:** Advanced Sidebar Filtering (Category, Brand, Price Slider).
- [x] **REQ-14:** Global Navbar Search (FlexSearch integration).
- [x] **REQ-15:** Detailed Product View with Dynamic Variant Selection.

## 5. Wishlist & Personalization
- [x] **REQ-16:** Persistent Wishlist for Authenticated Users.
- [x] **REQ-17:** Accurate Pricing Logic for Wishlisted Items (Net Price calculation).

## 6. Site Settings & Configuration
- [x] **REQ-18:** General Settings (Logo, SEO, Currency, visual assets).
- [x] **REQ-19:** Dynamic SMTP Mail Configuration (DB-driven).
- [x] **REQ-20:** Homepage Section Management (Visibility & Content Source for Bestsellers/Featured).

## 7. Shopping Cart System
- [x] **REQ-21:** Hybrid Cart Management (Database for Users / Session for Guests).
- [x] **REQ-22:** Cart UI Integration (Cart Page, Topbar Mini-cart, Mobile Navbar).
- [x] **REQ-23:** Cart Page UI Alignment (Banner height matched to Cart Total card).

## 8. Mobile UI Refinements
- [x] **REQ-24:** Mobile Navbar Alignment (Logo and Icons spacing fix).
- [x] **REQ-25:** Search Category Vertical Alignment (Fix for "All categories" text position).
- [x] **REQ-26:** Database Seeder Alignment (Fix seeders to match current model schemas and fields).
- [x] **REQ-27:** Cart Module Architectural Refactoring (Ensure full adherence to Service Layer and Form Request patterns).
- [x] **REQ-28:** Admin Management Architectural Refactoring (Refactor Admin CRUD to Service Layer and Form Request patterns).
- [x] **REQ-29:** Sidebar UI Refinement (Hide unlinked menus, link Users to Admin CRUD).
- [x] **REQ-31:** Admin Route Conflict Resolution (Rename public/admin to public/admin_assets to avoid 403 Forbidden errors).
