# TODO List - E-Commerce System

## Project Overview
Laravel + Filament e-commerce system dengan user registration, shopping cart, checkout, dan invoice generation.

## 📋 Complete Task List (22 Tasks)

### 🔐 User Registration & Authentication (Tasks 1-4)

- [ ] **Task 1**: Enable registration in UserPanelProvider
- [ ] **Task 2**: Create custom registration page for User Panel  
- [ ] **Task 3**: Implement registration logic with default role 'user'
- [ ] **Task 4**: Create UserOnly middleware if not exists

### 🗄️ Database Setup (Tasks 5-8)

- [ ] **Task 5**: Create migration for carts table
- [ ] **Task 6**: Create migration for orders table
- [ ] **Task 7**: Create migration for order_items table
- [ ] **Task 8**: Create migration for invoices table

### 📦 Models & Relationships (Tasks 9-11)

- [ ] **Task 9**: Create Cart model and relationship
- [ ] **Task 10**: Create Order model and relationships
- [ ] **Task 11**: Create Invoice model and relationships

### 🛒 E-Commerce Features (Tasks 12-14)

- [ ] **Task 12**: Create shopping cart functionality
- [ ] **Task 13**: Create checkout process
- [ ] **Task 14**: Create invoice generation and PDF printing

### 👨‍💻 Admin Panel (Tasks 15-17)

- [ ] **Task 15**: Create User Resource for Filament admin
- [ ] **Task 16**: Create Order Resource for Filament admin
- [ ] **Task 17**: Create Invoice Resource for Filament admin

### 🎨 Frontend Pages (Tasks 18-21)

- [ ] **Task 18**: Create frontend pages for products catalog
- [ ] **Task 19**: Create frontend cart page
- [ ] **Task 20**: Create frontend checkout page
- [ ] **Task 21**: Create user dashboard page

### 🧪 Testing (Task 22)

- [ ] **Task 22**: Test complete e-commerce flow

## 🏗️ System Architecture

### User Flow
```
👤 Registration → 🛒 Keranjang → 💳 Checkout → 🧾 Invoice
```

### Panel Structure
- **Admin Panel** (`/admin`) - Management system
- **User Panel** (`/user`) - Customer dashboard

### Database Schema
```sql
Users (✅ existing)
├── id, name, email, password, role (admin/user)

Carts (🔨 to create)
├── id, user_id, product_id, quantity, price_snapshot

Orders (🔨 to create)  
├── id, user_id, total_price, status, shipping_address

Order Items (🔨 to create)
├── id, order_id, product_id, quantity, price

Invoices (🔨 to create)
├── id, order_id, invoice_number, total, created_at
```

### Frontend Structure
```
/ (Home)
├── /products (Catalog)
├── /cart (Keranjang)
├── /checkout (Checkout)
├── /register (Daftar)
├── /login (Masuk)
└── /profile (User Dashboard)
```

### Admin Panel Structure
```
👥 Manajemen User
📦 Manajemen Produk  
📊 Orders
🧾 Invoices
```

## 📝 Notes

### Current Setup
- ✅ Laravel 10.x with Filament 3.x
- ✅ User model with role field
- ✅ Product model with category field
- ✅ Admin & User Panel providers
- ✅ Product Resource exists

### Dependencies
- Laravel Breeze (already installed)
- Filament (already installed)
- Need: PDF library for invoice generation
- Need: Email configuration for verification

### Next Steps
1. Start with Task 1-4 (User Registration)
2. Continue with database setup (Tasks 5-8)
3. Build models and relationships (Tasks 9-11)
4. Implement e-commerce features (Tasks 12-14)
5. Create admin resources (Tasks 15-17)
6. Build frontend pages (Tasks 18-21)
7. Complete testing (Task 22)

---
*Generated: 2025-12-17*
*Project: D:\webprok*