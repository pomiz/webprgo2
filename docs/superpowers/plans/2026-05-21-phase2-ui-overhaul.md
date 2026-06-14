# Phase 2: UI Overhaul - Elegant and Premium Theme

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox syntax for tracking.

**Goal:** Unify CSS framework to Tailwind CSS with an Elegant and Premium theme (dark tones, serif headings, gold accents). All user views extend a single layout.

**Architecture:** Remove Bootstrap CDN, redesign all user-facing Blade views using Tailwind CSS utility classes. Create a shared layout component that all pages extend.

**Tech Stack:** Tailwind CSS 3.x, Alpine.js, Blade Components, Google Fonts (Playfair Display + Inter)

---

## Task 1: Configure Tailwind for Elegant Theme

**Files:**
- Modify: `tailwind.config.js`
- Modify: `resources/css/app.css`

- [ ] **Step 1: Update tailwind.config.js with custom theme**

Add custom font families (Inter for body, Playfair Display for headings), brand colors (warm gold palette), and surface colors (dark slate tones for dark mode).

- [ ] **Step 2: Update resources/css/app.css with base styles**

Add Google Fonts import, base layer styles for body and headings, component layer with btn-primary, btn-outline, and card classes.

- [ ] **Step 3: Verify Tailwind compiles**

Run: `npm run build`
Expected: No errors, CSS output generated

- [ ] **Step 4: Commit**

```bash
git add tailwind.config.js resources/css/app.css
git commit -m "feat: configure Tailwind with Elegant and Premium theme"
```

---

## Task 2: Create Shared User Layout

**Files:**
- Create: `resources/views/layouts/user.blade.php`

- [ ] **Step 1: Create user layout with navigation, flash messages, footer**

Layout includes:
- Sticky nav with logo (font-serif), nav links, dark mode toggle (Alpine.js + localStorage), cart icon with count badge, user dropdown menu
- Flash message area for success/error
- Main content area with `@yield('content')`
- Footer with copyright

- [ ] **Step 2: Commit**

```bash
git add resources/views/layouts/user.blade.php
git commit -m "feat: create shared user layout with elegant premium theme"
```

---

## Task 3: Redesign Home Page

**Files:**
- Modify: `resources/views/user/home.blade.php`

- [ ] **Step 1: Rewrite home.blade.php extending layouts.user**

Remove all Bootstrap CDN links and inline styles. Use `@extends('layouts.user')` and `@section('content')`. Includes:
- Hero section with gradient overlay on image
- Search bar with Tailwind form styling
- Category filter pills (btn-primary for active, btn-outline for inactive)
- Product grid (3 columns on desktop, responsive) with card component
- Each card: aspect-ratio image with hover scale, category badge, serif title, price, detail link
- Empty state with icon and CTA

- [ ] **Step 2: Verify page renders**

Run: `php artisan view:clear` then check in browser

- [ ] **Step 3: Commit**

```bash
git add resources/views/user/home.blade.php
git commit -m "feat: redesign home page with elegant premium Tailwind theme"
```

---

## Task 4: Redesign Product Detail Page

**Files:**
- Modify: `resources/views/user/detail.blade.php`

- [ ] **Step 1: Rewrite detail page extending layouts.user**

Two-column layout:
- Left: large product image with rounded corners
- Right: category badge, product name (font-serif), description, price, stock indicator, quantity input, add-to-cart button
- Below: prev/next product navigation
- Below: reviews section placeholder (will be implemented in Phase 4)

- [ ] **Step 2: Commit**

```bash
git add resources/views/user/detail.blade.php
git commit -m "feat: redesign product detail page with elegant theme"
```

---

## Task 5: Redesign Cart Page

**Files:**
- Modify: `resources/views/user/cart.blade.php`

- [ ] **Step 1: Rewrite cart page with Tailwind, using cartItems from DB**

Layout:
- Page title (font-serif)
- Cart items list: each item shows image thumbnail, product name, unit price, quantity, subtotal, checkbox for selection, delete button (DELETE form with CSRF)
- Summary sidebar: selected items count, subtotal, checkout button
- Empty cart state with CTA to browse products

- [ ] **Step 2: Commit**

```bash
git add resources/views/user/cart.blade.php
git commit -m "feat: redesign cart page with elegant theme and DB-backed items"
```

---

## Task 6: Redesign Checkout and Invoice Page

**Files:**
- Modify: `resources/views/checkout/invoice.blade.php`

- [ ] **Step 1: Rewrite invoice with elegant styling**

Clean invoice layout:
- Header with store name and order date
- Order number and status badge (colored based on status)
- Items table: product name, qty, unit price, line total
- Summary: subtotal, shipping cost (placeholder for Phase 3), total
- Virtual account number in highlighted box
- Print button

- [ ] **Step 2: Commit**

```bash
git add resources/views/checkout/invoice.blade.php
git commit -m "feat: redesign invoice page with elegant premium theme"
```

---

## Task 7: Redesign Products List Page

**Files:**
- Modify: `resources/views/user/products.blade.php`

- [ ] **Step 1: Rewrite products page extending layouts.user**

Similar grid layout to home but without hero section. Full product catalog with search and filter.

- [ ] **Step 2: Commit**

```bash
git add resources/views/user/products.blade.php
git commit -m "feat: redesign products list page with elegant theme"
```

---

## Task 8: Clean Up Old Assets

**Files:**
- Modify: Any remaining views that still reference Bootstrap CDN

- [ ] **Step 1: Search and remove all Bootstrap CDN references**

Run: `grep -r "bootstrap" resources/views/`
Remove any remaining CDN links to Bootstrap CSS/JS and Bootstrap Icons.

- [ ] **Step 2: Run full build and verify**

Run: `npm run build`
Visit all pages in browser to confirm no broken styles.

- [ ] **Step 3: Commit**

```bash
git add .
git commit -m "chore: remove all Bootstrap CDN references, fully migrated to Tailwind"
```
