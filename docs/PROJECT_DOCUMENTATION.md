# Ruang Baju - Dokumentasi Project

## Overview

Ruang Baju adalah aplikasi e-commerce fashion untuk pakaian unisex anak hingga remaja. Dibangun dengan Laravel 10, Filament 3 (admin panel), Tailwind CSS dengan glassmorphism UI, dan Alpine.js.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 10, PHP 8.1+ |
| Admin Panel | Filament 3.3 |
| Database | MySQL |
| Frontend | Blade, Tailwind CSS 3, Alpine.js |
| Build Tool | Vite 5 |
| Auth | Laravel Breeze + Socialite (Google SSO) |
| Testing | Pest |

---

## Setup & Installation

```bash
# 1. Clone & install dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database (pastikan MySQL running, konfigurasi .env)
php artisan migrate:fresh --seed

# 4. Storage link (untuk gambar produk)
php artisan storage:link

# 5. Build frontend
npm run build

# 6. Jalankan server
php artisan serve
```

**Development mode (hot reload):**
```bash
php artisan serve        # Terminal 1
npm run dev              # Terminal 2
```

**Default credentials:**
- Admin: `admin@ruangbaju.com` / `admin12345`
- User: `faruq@ruangbaju.com` / `faruq1234`

---

## Architecture

```
app/
├── Filament/              # Admin panel (Resources, Pages, Widgets)
│   ├── Pages/             # StoreSettings
│   ├── Resources/         # ProductResource, UserResource, OrderResource
│   └── Widgets/           # StatsOverview, OrderChart, RecentOrders
├── Http/
│   ├── Controllers/       # Lean controllers
│   │   ├── Auth/          # SocialAuthController, Breeze controllers
│   │   ├── Admin/         # ProductPrintController, UserPrintController
│   │   ├── CheckoutController.php
│   │   ├── ProductController.php
│   │   ├── ProfileController.php
│   │   ├── ReviewController.php
│   │   ├── ShippingController.php
│   │   └── UserController.php
│   └── Requests/          # CheckoutRequest, StoreReviewRequest
├── Models/                # Eloquent models
│   ├── CartItem.php
│   ├── Location.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Product.php
│   ├── Review.php
│   ├── StoreSetting.php
│   ├── User.php
│   └── UserAddress.php
└── Services/              # Business logic
    ├── CheckoutService.php
    └── ShippingService.php
```

---

## Database Schema

### users
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| username | string | unique |
| name | string | nullable |
| email | string | unique |
| google_id | string | nullable, unique (SSO) |
| avatar | string | nullable |
| password | string | nullable (SSO users) |
| role | enum | admin / user |
| email_verified_at | timestamp | nullable |
| timestamps | | |

### products
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| name | string | |
| description | text | |
| category | string | |
| price | decimal(12,2) | |
| stock | integer | |
| image | string | |
| timestamps | | |

### orders
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| user_id | FK | users.id |
| subtotal | decimal(12,2) | |
| shipping_cost | decimal(12,2) | |
| shipping_address | string | nullable |
| total_price | decimal(12,2) | subtotal + shipping |
| status | string | see Order Status Flow |
| virtual_account | string | |
| timestamps | | |

### order_items
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| order_id | FK | orders.id |
| product_id | FK | products.id |
| quantity | integer | |
| price | decimal | snapshot harga saat beli |
| timestamps | | |

### cart_items
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| user_id | FK | users.id |
| product_id | FK | products.id |
| quantity | integer | |
| timestamps | | |
| | | unique(user_id, product_id) |

### reviews
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| user_id | FK | users.id |
| product_id | FK | products.id |
| rating | tinyint | 1-5 |
| comment | text | nullable |
| timestamps | | |
| | | unique(user_id, product_id) |

### store_settings
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| store_name | string | |
| store_city | string | nullable |
| store_province | string | nullable |
| store_latitude | decimal(10,7) | |
| store_longitude | decimal(10,7) | |
| shipping_rate_per_km | decimal(10,2) | |
| min_shipping_cost | decimal(10,2) | |
| max_shipping_cost | decimal(10,2) | |
| timestamps | | |

### locations
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| province | string | |
| city | string | |
| district | string | nullable |
| latitude | decimal(10,7) | |
| longitude | decimal(10,7) | |
| timestamps | | |

### user_addresses
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| user_id | FK | users.id |
| label | string | default: 'Rumah' |
| province | string | nullable |
| city | string | nullable |
| district | string | nullable |
| full_address | text | nullable |
| latitude | decimal(10,7) | |
| longitude | decimal(10,7) | |
| is_default | boolean | |
| timestamps | | |

---

## Order Status Flow

```
pending_payment ──→ confirmed ──→ processing ──→ shipped ──→ completed
       │                │              │
       └────────────────┴──────────────┴──→ cancelled
```

| Status | Label | Warna |
|--------|-------|-------|
| pending_payment | Menunggu Pembayaran | yellow |
| confirmed | Pembayaran Dikonfirmasi | blue |
| processing | Sedang Dikemas | indigo |
| shipped | Dikirim | purple |
| completed | Selesai | green |
| cancelled | Dibatalkan | red |

Admin mengubah status via Filament panel (`/admin/orders`).

---

## API Endpoints (Frontend)

Semua endpoint memerlukan autentikasi (middleware `auth`). CSRF token dikirim via `X-CSRF-TOKEN` header dari meta tag.

### Shipping

| Method | Endpoint | Params | Response |
|--------|----------|--------|----------|
| GET | `/shipping/provinces` | - | `["DKI Jakarta", "Jawa Barat", ...]` |
| GET | `/shipping/cities` | `?province=DKI Jakarta` | `[{"city": "Jakarta Pusat", "latitude": -6.18, "longitude": 106.83}, ...]` |
| POST | `/shipping/calculate` | `{"latitude": float, "longitude": float}` | `{"distance_km": 7.02, "cost": 14044, "error": null}` |
| POST | `/shipping/save-address` | `{"province": str, "city": str, "full_address": str, "latitude": float, "longitude": float}` | `{"success": true, "address": {...}}` |

**Shipping calculation formula:**
```
distance = haversine(store_lat, store_lng, user_lat, user_lng)
cost = clamp(distance * rate_per_km, min_cost, max_cost)
```

### Cart

| Method | Endpoint | Params | Response |
|--------|----------|--------|----------|
| POST | `/add-to-cart/{id}` | `quantity` (form) | Redirect back + flash |
| DELETE | `/cart/{id}` | CSRF | Redirect back + flash |

### Checkout

| Method | Endpoint | Params | Response |
|--------|----------|--------|----------|
| POST | `/checkout/prepare` | `selected_products[]` (array of product IDs) | Redirect to `/checkout` |
| GET | `/checkout` | - | Checkout page (requires prepare first) |
| POST | `/checkout` | `shipping_address`, `latitude`, `longitude`, `province`, `city` | Redirect to invoice |

**Note:** `shipping_cost` dihitung server-side dari koordinat. Client value diabaikan.

### Reviews

| Method | Endpoint | Params | Response |
|--------|----------|--------|----------|
| POST | `/product/{product}/review` | `rating` (1-5), `comment` (optional) | Redirect to product detail |
| DELETE | `/product/{product}/review` | CSRF | Redirect to product detail |

---

## Frontend Pages

### User-Facing (requires auth)

| Route | View | Description |
|-------|------|-------------|
| `GET /` | `user/home` | Homepage: hero, search, category filter, product grid |
| `GET /products` | `user/products` | Full product catalog |
| `GET /product/{id}` | `user/detail` | Product detail + reviews |
| `GET /cart` | `user/cart` | Shopping cart |
| `GET /checkout` | `checkout/index` | Address selection + shipping calc |
| `GET /invoice/{order}` | `checkout/invoice` | Order invoice |
| `GET /orders` | `user/orders` | Order history |
| `GET /profile` | `profile/edit` | Edit profile, password, delete account |

### Auth Pages (guests)

| Route | View | Description |
|-------|------|-------------|
| `GET /login` | `auth/login` | Login (email + Google SSO) |
| `GET /register` | `auth/register` | Register (email + Google SSO) |
| `GET /auth/google/redirect` | - | Redirect to Google OAuth |
| `GET /auth/google/callback` | - | Handle Google OAuth callback |

### Admin Panel (`/admin`)

| Route | Description |
|-------|-------------|
| `/admin` | Dashboard (stats, chart, recent orders) |
| `/admin/orders` | Order management (status progression) |
| `/admin/products` | Product CRUD |
| `/admin/users` | User management |
| `/admin/store-settings` | Store location & shipping config |

---

## UI Components

### Blade Components (`resources/views/components/`)

| Component | Props | Usage |
|-----------|-------|-------|
| `<x-star-rating>` | `:rating`, `size` | Display star rating (read-only) |
| `<x-review-form>` | `:product`, `:existingReview` | Interactive review form (Alpine.js) |
| `<x-review-list>` | `:reviews` | List of reviews with delete option |

### CSS Classes (Tailwind `@layer components`)

| Class | Description |
|-------|-------------|
| `.btn-primary` | Gold button with shadow + hover lift |
| `.btn-outline` | Outlined gold button with backdrop blur |
| `.card` | Glassmorphism card (semi-transparent, blur, border) |
| `.glass` | Generic glass effect |
| `.glass-nav` | Navigation bar glass effect |
| `.glass-card` | Stronger glass card (for auth pages) |
| `.glass-input` | Semi-transparent input field |

### Theme Colors

| Token | Usage |
|-------|-------|
| `brand-50` to `brand-950` | Warm gold palette (primary actions) |
| `surface-50` to `surface-950` | Dark slate (backgrounds, dark mode) |

### Typography

| Font | Usage |
|------|-------|
| Inter | Body text (`font-sans`) |
| Playfair Display | Headings (`font-serif`) |

---

## Testing

```bash
# Run all tests
php artisan test

# Run specific suite
php artisan test tests/Feature/CartTest.php
php artisan test tests/Feature/CheckoutTest.php
php artisan test tests/Feature/ReviewTest.php
php artisan test tests/Feature/ShippingTest.php
php artisan test tests/Unit/ShippingServiceTest.php
php artisan test tests/Unit/StoreSettingTest.php
```

**Test coverage:**
- Cart: add, increment, remove, view, auth guard
- Checkout: prepare, validation, process, stock check, invoice access
- Reviews: create, update, delete, validation, auth guard
- Shipping: provinces, cities, calculate, save address, validation
- Auth: login, register, logout
- Profile: view, update, delete

---

## Services

### ShippingService (`app/Services/ShippingService.php`)

```php
calculateDistance(lat1, lon1, lat2, lon2): float  // Haversine formula, returns km
calculateShippingCost(userLat, userLon): array    // Returns {distance_km, cost, error}
```

### CheckoutService (`app/Services/CheckoutService.php`)

```php
getCartItems(userId, productIds): Collection
validateStock(cartItems): ?string           // null = valid, string = error message
calculateSubtotal(cartItems): float
calculateShipping(lat, lng): float
processOrder(userId, cartItems, data): Order|string  // Order on success, error string on fail
```

---

## Security

- All user routes protected by `auth` middleware
- Admin routes protected by `can:admin` gate
- CSRF protection on all forms
- Shipping cost calculated server-side (client value ignored)
- Atomic stock decrement prevents overselling
- Social auth provider whitelist (only `google`)
- Password hashed via model cast (no manual Hash::make needed)
- Invoice access restricted to order owner

---

## Environment Variables

```env
# App
APP_NAME="Ruang Baju"
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webprok
DB_USERNAME=root
DB_PASSWORD=

# Google SSO (optional)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```
