# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

**Ruang Baju** — Laravel 10 + Filament 3 e-commerce app (Indonesian fashion store). MySQL backend, Blade + Tailwind + Alpine frontend, Pest for tests. Deeper reference lives in `docs/PROJECT_DOCUMENTATION.md`.

## Commands

```bash
# First-time setup
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
npm run build

# Dev (two terminals)
php artisan serve
npm run dev

# Tests (Pest)
php artisan test                          # all
php artisan test --filter=CheckoutTest    # one file
php artisan test tests/Feature/CartTest.php::it_adds_item_to_cart  # one test
vendor/bin/pest --parallel                # parallel run

# Code style
vendor/bin/pint                           # Laravel Pint formatter
```

Default seeded accounts: admin `admin@ruangbaju.com` / `admin12345`, user `faruq@ruangbaju.com` / `faruq1234`.

## Architecture

### Two Filament panels
The app runs **two** Filament panels side by side, each with its own provider in `app/Providers/Filament/`:
- `AdminPanelProvider` mounts at `/admin` and is the `default()` panel — Resources in `app/Filament/Resources/` (Product/User/Order), Widgets in `app/Filament/Widgets/`.
- `UserPanelProvider` mounts at `/user`, gated by `App\Http\Middleware\UserOnly` — its own resource/page namespaces under `app/Filament/User/`.

When adding admin-side CRUD, put it under `app/Filament/Resources/`. User-panel pages go under `app/Filament/User/`.

### Auth-gated frontend
**All** custom web routes in `routes/web.php` sit inside a `Route::middleware(['auth'])->group(...)` — there is no public product browsing. The `/` route also branches by role: admins are redirected to the Filament dashboard, users get the storefront via `UserController@index`. Admin-only Blade routes (`/print/*`) are further gated by `Route::middleware(['can:admin'])`, where `admin` is a Gate defined in `AuthServiceProvider` (`$user->role === 'admin'`).

`User::canAccessFilament()` enforces admin role at the Filament layer.

### Service layer for business logic
Controllers stay thin; multi-step domain logic lives in `app/Services/`:
- `CheckoutService` — owns the order-creation transaction: validates stock, calculates subtotal + shipping, decrements stock atomically with `where('stock', '>=', $qty)->decrement(...)`, clears cart, persists default address. Returns `Order|string` (string = error message). Wrap any new checkout side-effect inside its `DB::beginTransaction()` block.
- `ShippingService` — Haversine distance from `StoreSetting` coordinates × `shipping_rate_per_km`, clamped to `min_shipping_cost` / `max_shipping_cost`.

Inject services via constructor promotion, as `CheckoutController` and `CheckoutService` already do.

### Cart → Checkout flow
Cart is **DB-backed** (`cart_items` table, `CartItem` model) keyed by `user_id` — not session-based. The session is only used to pass the *selected* product IDs from cart to checkout: `CheckoutController@prepare` writes `checkout_products` to the session, `showCheckout`/`checkout` read it, and a successful order forgets the key. Don't reintroduce a session cart.

### Order state machine
`Order` model owns its own state machine via `STATUSES` constants and `advance()`/`cancel()` methods. The flow is `pending_payment → confirmed → processing → shipped → completed`, with `cancel()` allowed only from the first three. Always mutate status through these methods so transitions stay valid.

### Frontend assets
Vite + Tailwind. Entry points are declared in `vite.config.js`; Blade templates under `resources/views/` use `@vite(...)`. Run `npm run dev` for HMR during view work.

## Conventions

- Migrations are timestamped per the actual creation date — keep them ordered; don't renumber existing ones.
- Routes are named (`cart.index`, `checkout.process`, etc.) — reference them via `route('name')` rather than hardcoded paths so the centralized `routes/web.php` stays the source of truth.
- User-facing strings are Indonesian (e.g. `'Stok untuk produk X tidak mencukupi.'`) — match the existing language when adding flash messages.
- `User` model has `'password' => 'hashed'` cast, so never wrap passwords with `Hash::make()` again in factories or seeders (a recent commit cleaned this up).
