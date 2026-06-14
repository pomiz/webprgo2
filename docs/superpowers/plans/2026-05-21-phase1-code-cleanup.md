# Phase 1: Code Cleanup & Security Fixes

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Fix security issues, dead code, route protection, and architectural inconsistencies before adding new features.

**Architecture:** Fix existing Laravel 10 app - protect routes, clean dead code, standardize cart to DB-based, fix HTTP methods.

**Tech Stack:** Laravel 10, PHP 8.1+, MySQL

---

## Task 1: Fix Dead Code in User Model

**Files:**
- Modify: `app/Models/User.php`

- [ ] **Step 1: Remove unreachable code and add HasApiTokens**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessFilament(): bool
    {
        return $this->role === 'admin';
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
```

- [ ] **Step 2: Verify no errors**

Run: `php artisan tinker --execute="new App\Models\User;"`
Expected: No errors

- [ ] **Step 3: Commit**

```bash
git add app/Models/User.php
git commit -m "fix: remove dead code in User model, add HasApiTokens and relations"
```

---

## Task 2: Protect Unprotected Routes

**Files:**
- Modify: `routes/web.php`

- [ ] **Step 1: Move dashboard and print routes inside auth middleware group**

Replace the unprotected routes at the bottom of `web.php`:

```php
// These routes need auth + admin protection
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('filament.admin.pages.dashboard');
    })->name('dashboard');

    Route::get('/print/products', ProductPrintController::class)->name('print.product');
    Route::get('/print/users', UserPrintController::class)->name('print.user');
});
```

- [ ] **Step 2: Verify routes are protected**

Run: `php artisan route:list --path=print`
Expected: Routes show `auth` middleware

- [ ] **Step 3: Commit**

```bash
git add routes/web.php
git commit -m "fix: protect dashboard and print routes with auth middleware"
```

---

## Task 3: Fix Cart Route - GET to DELETE

**Files:**
- Modify: `routes/web.php`

- [ ] **Step 1: Change remove-from-cart from GET to DELETE**

In the auth middleware group, replace:
```php
Route::get('/remove-from-cart/{id}', [UserController::class, 'removeFromCart'])->name('cart.remove');
```

With:
```php
Route::delete('/cart/{id}', [UserController::class, 'removeFromCart'])->name('cart.remove');
```

- [ ] **Step 2: Update cart view to use DELETE form**

In any view that uses the remove-from-cart link, replace the `<a>` tag with a form:

```html
<form action="{{ route('cart.remove', $id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
</form>
```

- [ ] **Step 3: Commit**

```bash
git add routes/web.php resources/views/user/cart.blade.php
git commit -m "fix: change remove-from-cart from GET to DELETE method"
```

---

## Task 4: Migrate Cart from Session to Database

**Files:**
- Modify: `app/Http/Controllers/UserController.php`
- Modify: `app/Models/CartItem.php`
- Delete: `app/Http/Controllers/CartController.php` (unused duplicate)

- [ ] **Step 1: Rewrite UserController cart methods to use CartItem model**

```php
// In UserController.php - replace addToCart method:
public function addToCart(Request $request, $id)
{
    $product = Product::findOrFail($id);
    $quantity = max(1, (int) $request->input('quantity', 1));

    $cartItem = CartItem::where('user_id', auth()->id())
        ->where('product_id', $id)
        ->first();

    if ($cartItem) {
        $cartItem->increment('quantity', $quantity);
    } else {
        CartItem::create([
            'user_id' => auth()->id(),
            'product_id' => $id,
            'quantity' => $quantity,
        ]);
    }

    return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
}

// Replace cart method:
public function cart()
{
    $cartItems = CartItem::where('user_id', auth()->id())
        ->with('product')
        ->get();

    return view('user.cart', compact('cartItems'));
}

// Replace removeFromCart method:
public function removeFromCart($id)
{
    CartItem::where('user_id', auth()->id())
        ->where('product_id', $id)
        ->delete();

    return redirect()->back()->with('success', 'Produk dihapus dari keranjang.');
}
```

- [ ] **Step 2: Update CheckoutController to use CartItem model**

Replace session-based cart logic with database queries:

```php
public function checkout(Request $request)
{
    $selectedProductIds = $request->input('selected_products');
    if (empty($selectedProductIds)) {
        return redirect()->route('cart.index')->with('error', 'Tidak ada produk yang dipilih untuk checkout.');
    }

    $cartItems = CartItem::where('user_id', auth()->id())
        ->whereIn('product_id', $selectedProductIds)
        ->with('product')
        ->get();

    if ($cartItems->isEmpty()) {
        return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
    }

    $totalPrice = 0;
    foreach ($cartItems as $item) {
        if ($item->product->stock < $item->quantity) {
            return redirect()->route('cart.index')
                ->with('error', 'Stok untuk produk ' . $item->product->name . ' tidak mencukupi.');
        }
        $totalPrice += $item->product->price * $item->quantity;
    }

    $order = null;

    try {
        DB::beginTransaction();

        $order = Order::create([
            'user_id' => auth()->id(),
            'total_price' => $totalPrice,
            'status' => 'pending',
            'virtual_account' => 'VA' . date('Ymd') . Str::upper(Str::random(8)),
        ]);

        foreach ($cartItems as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);

            $item->product->decrement('stock', $item->quantity);
        }

        // Remove checked-out items from cart
        CartItem::where('user_id', auth()->id())
            ->whereIn('product_id', $selectedProductIds)
            ->delete();

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('cart.index')
            ->with('error', 'Terjadi kesalahan saat memproses pesanan.');
    }

    return redirect()->route('invoice.show', $order);
}
```

- [ ] **Step 3: Delete unused CartController**

```bash
del app/Http/Controllers/CartController.php
```

- [ ] **Step 4: Verify cart works**

Run: `php artisan tinker --execute="App\Models\CartItem::count();"`
Expected: Returns a number (0 if fresh DB)

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/UserController.php app/Http/Controllers/CheckoutController.php
git rm app/Http/Controllers/CartController.php
git commit -m "refactor: migrate cart from session-based to database-backed CartItem model"
```

---

## Task 5: Add Form Request Validation for Checkout

**Files:**
- Create: `app/Http/Requests/CheckoutRequest.php`
- Modify: `app/Http/Controllers/CheckoutController.php`

- [ ] **Step 1: Create CheckoutRequest**

Run: `php artisan make:request CheckoutRequest`

Then set contents:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'selected_products' => ['required', 'array', 'min:1'],
            'selected_products.*' => ['integer', 'exists:products,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'selected_products.required' => 'Pilih minimal satu produk untuk checkout.',
            'selected_products.min' => 'Pilih minimal satu produk untuk checkout.',
        ];
    }
}
```

- [ ] **Step 2: Use CheckoutRequest in controller**

In `CheckoutController.php`, change method signature:

```php
use App\Http\Requests\CheckoutRequest;

public function checkout(CheckoutRequest $request)
{
    $selectedProductIds = $request->validated()['selected_products'];
    // ... rest of logic
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Requests/CheckoutRequest.php app/Http/Controllers/CheckoutController.php
git commit -m "feat: add form request validation for checkout"
```
