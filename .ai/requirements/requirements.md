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
- [x] **REQ-10:** Product Variant Management (Size, Color, SKU, Stock).
- [x] **REQ-11:** Multi-Image Gallery with Primary Image selection.

## 4. Customer Shop Frontend
- [x] **REQ-12:** Dynamic Product Listing (Grid & List views).
- [x] **REQ-13:** Advanced Sidebar Filtering (Category, Brand, Size, Color, Price Slider).
- [x] **REQ-14:** Global Navbar Search with Category Targeting (FlexSearch).
- [x] **REQ-15:** Detailed Product View with Dynamic Variant Selection.

## 5. Wishlist & Personalization
- [x] **REQ-16:** Persistent Wishlist for Authenticated Users.
- [x] **REQ-17:** Accurate Pricing Logic for Wishlisted Items (Net Price calculation).

## 6. Site Settings & Configuration
- [x] **REQ-18:** General Settings (Logo, SEO, Currency, visual assets).
- [x] **REQ-19:** Dynamic SMTP Mail Configuration (DB-driven).
- [x] **REQ-20:** Homepage Section Management (Visibility & Content Source for Bestsellers/Featured).
