# 🛍️ Ruang Baju - E-Commerce Fashion Store

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.10-red" alt="Laravel">
  <img src="https://img.shields.io/badge/Filament-3.3-orange" alt="Filament">
  <img src="https://img.shields.io/badge/Tailwind-3.4-38B2AC" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/PHP-8.1+-777BB4" alt="PHP">
</p>

Aplikasi e-commerce modern untuk toko baju online "Ruang Baju" yang menjual fashion unisex dari anak-anak hingga remaja.

## 📋 Project Overview

**Ruang Baju** adalah aplikasi e-commerce Laravel-based yang menyediakan platform lengkap untuk berjualan fashion online dengan fitur:

- 🛍️ Product browsing dengan kategori filter
- 🛒 Session-based shopping cart
- 📱 WhatsApp ordering integration
- 👤 User authentication & profile management
- 🎨 Dark mode toggle
- 📱 Responsive mobile design
- 🛠️ Modern admin panel dengan Filament

---

## 🏗️ Architecture Overview

### **Tech Stack**
- **Backend:** Laravel v10.10 (PHP 8.1+)
- **Admin Panel:** Filament v3.3
- **Frontend:** Blade Templates + Tailwind CSS
- **Authentication:** Laravel Breeze
- **Build Tool:** Vite
- **JavaScript:** Alpine.js

### **Project Structure**
```
📦 D:\webprok\
├── 🎮 Controllers Layer
│   ├── UserController.php (Main Public Controller)
│   ├── ProductController.php (Basic Product Listing)
│   ├── ProfileController.php (User Profile Management)
│   └── Auth/ (9 Authentication Controllers)
│
├── 📊 Models Layer
│   ├── User.php (User dengan role-based access)
│   └── Product.php (Product model)
│
├── 🎨 Views Layer
│   ├── user/ (Customer Facing Views)
│   ├── auth/ (Authentication Views)
│   ├── layouts/ (Template Layouts)
│   └── filament/ (Admin Panel Views)
│
├── 🗄️ Database
│   ├── migrations/ (8 migration files)
│   └── seeders/ (ProductSeeder, DatabaseSeeder)
│
└── 🛣️ Routes
    ├── web.php (Main application routes)
    └── auth.php (Authentication routes)
```

---

## 🎮 Controllers Technical Analysis

### **1. UserController.php** (Main Public Controller)

**Tugas Utama:** Menghandle semua interaksi user-facing (customer)

#### **Method Details:**

| Method | Route | Tugas | Technical Implementation |
|--------|-------|-------|-------------------------|
| `index(Request $request)` | `GET /` | Homepage dengan product listing & filter kategori | `Product::select('category')->distinct()->pluck('category')` + `Product::when($category)->latest()->get()` |
| `show($id)` | `GET /product/{id}` | Detail produk | `Product::findOrFail($id)` dengan auto 404 handling |

**Session Cart Structure:**
```php
[
    $productId => [
        "name" => $product->name,
        "quantity" => 1,
        "price" => $product->price,
        "image" => $product->image,
    ]
]
```

### **2. ProductController.php** (Basic Product Controller)

**Tugas:** Basic product listing (minimal implementation)

| Method | Route | Status | Note |
|--------|-------|--------|------|
| `index()` | Not registered | ⚠️ Unused | `Product::all()` tanpa filter |

### **3. ProfileController.php** (User Profile Management)

**Tugas:** Mengelola user profile (Laravel Breeze default)

| Method | Route | Middleware | Features |
|--------|-------|------------|----------|
| `edit()` | `GET /profile` | `auth` | Form edit profile |
| `update()` | `PATCH /profile` | `auth` | Profile update dengan email verification reset |
| `destroy()` | `DELETE /profile` | `auth` | Account deletion dengan password confirmation |

**Security Features:**
- `ProfileUpdateRequest` validation
- Current password confirmation for deletion
- Session invalidate & regenerate on logout

### **4. Authentication Controllers** (Auth Folder)

**Tugas:** Menghandle semua proses authentication (Laravel Breeze)

#### **Key Controllers:**

| Controller | Methods | Features |
|------------|---------|----------|
| `AuthenticatedSessionController` | `create()`, `store()`, `destroy()` | Login/logout dengan session security |
| `RegisteredUserController` | `create()`, `store()` | Registration dengan auto-login dan default role `'user'` |
| `PasswordResetLinkController` | `create()`, `store()` | Password reset request |
| `NewPasswordController` | `create()`, `store()` | New password setup |
| `EmailVerification Controllers` | Multiple | Email verification process |

---

## 📊 Database Schema

### **Users Table**
```sql
- id (bigint, primary)
- name (string)
- email (string, unique)
- email_verified_at (timestamp, nullable)
- password (string)
- role (enum: 'admin', 'user', default: 'user')
- remember_token (string)
- created_at, updated_at (timestamps)
```

### **Products Table**
```sql
- id (bigint, primary)
- name (string)
- category (string, nullable) - Added via migration
- description (text, nullable)
- price (decimal, 12,2)
- stock (integer, default: 0)
- size (string, nullable) - S, M, L, XL
- color (string, nullable)
- image (string, nullable) - URL path
- created_at, updated_at (timestamps)
```

---

## 🛣️ Routing Structure

### **Public Routes (No Auth Required)**
```php
GET /                    → UserController@index (Homepage)
GET /product/{id}        → UserController@show (Detail)
```

### **Protected Routes (Auth Required)**
```php
GET /dashboard           → Admin dashboard (auth + verified)
GET /profile             → ProfileController@edit
PATCH /profile           → ProfileController@update
DELETE /profile          → ProfileController@destroy
```

### **Authentication Routes**
```php
POST /login              → AuthenticatedSessionController@store
POST /register           → RegisteredUserController@store
POST /logout             → AuthenticatedSessionController@destroy
```

---

## 🎯 Key Features & Implementation

### **Customer Features**
1. **Product Browsing**
   - Category-based filtering (Kaos, Kemeja, Celana, Jaket, Aksesoris)
   - Search functionality
   - Latest products sorting

2. **Order Integration**
   - WhatsApp direct ordering: `+62895359586490`
   - Auto-generated WhatsApp message with product details

3. **User Experience**
   - Dark mode toggle
   - Responsive mobile design
   - Bootstrap 5 + Tailwind CSS styling
   - Inter font typography

### **Admin Features**
1. **Filament Admin Panel**
   - Modern admin interface
   - Product CRUD operations
   - Image upload support
   - Category management

2. **Role-Based Access Control**
   - Admin-only Filament access via `User::canAccessFilament()`
   - Default user role for registrations

---

## 🔧 Technical Implementation Details

### **Security Features**
- **CSRF Protection:** Built-in Laravel
- **Password Hashing:** `Hash::make()`
- **Session Regeneration:** Login/logout security
- **Form Request Validation:** `ProfileUpdateRequest`, `LoginRequest`
- **Role-Based Access:** Admin-only areas

### **Session Management**
- **Session Security:** Regeneration on authentication
- **Session Invalidation:** On account deletion

### **Error Handling**
- **404 Handling:** `Product::findOrFail($id)`
- **Validation:** Form Request classes
- **User Feedback:** `->with('success', 'message')` redirects

---

## 🚀 Getting Started

### **Prerequisites**
- PHP 8.1+
- Composer
- Node.js & NPM
- Database (MySQL/PostgreSQL/SQLite)

### **Installation**
```bash
# Clone repository
git clone <repository-url>
cd webprok

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start development server
php artisan serve
```

### **Default Admin Account**
- **Email:** ipul@tokobaju.com
- **Password:** ipul12345
- **Role:** admin

### **Sample Products**
The ProductSeeder creates 4 sample products:
1. Kaos Unisex Oversize (Rp 75,000)
2. Hoodie Casual (Rp 120,000)
3. Celana Jogger (Rp 95,000)
4. Kemeja Kotak-Kotak (Rp 110,000)

---

## 📱 Application Screenshots

### **Customer Interface**
- **Homepage:** Product grid with category filters
- **Product Detail:** Split-screen layout with WhatsApp order button
- **Shopping Cart:** Session-based cart management
- **Authentication:** Modern login/register forms

### **Admin Interface**
- **Filament Dashboard:** Product management interface
- **Product CRUD:** Create, read, update, delete products
- **Image Upload:** Product image management

---

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🔗 Useful Links

- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Laravel Bootcamp](https://bootcamp.laravel.com)

---

<p align="center">
  <strong>Built with ❤️ using Laravel & Filament</strong>
</p>
