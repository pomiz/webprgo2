# Phase 4: Comment dan Rating Produk

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox syntax for tracking.

**Goal:** Allow authenticated users to leave star ratings (1-5) and text comments on products. Display average rating on product cards and full reviews on detail page. One review per user per product (editable).

**Architecture:** New `reviews` table, Review model with relations to User and Product. ReviewController handles CRUD. Display integrated into existing product views.

**Tech Stack:** Laravel 10, Blade, Tailwind CSS, Alpine.js (for star rating UI)

---

## Task 1: Create Reviews Migration and Model

**Files:**
- Create: `database/migrations/xxxx_create_reviews_table.php`
- Create: `app/Models/Review.php`
- Modify: `app/Models/Product.php`
- Modify: `app/Models/User.php`

- [ ] **Step 1: Create migration**

Run: `php artisan make:migration create_reviews_table`

```php
Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained()->onDelete('cascade');
    $table->tinyInteger('rating')->unsigned(); // 1-5
    $table->text('comment')->nullable();
    $table->timestamps();

    $table->unique(['user_id', 'product_id']); // one review per user per product
});
```

- [ ] **Step 2: Create Review model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = ['user_id', 'product_id', 'rating', 'comment'];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
```

- [ ] **Step 3: Add reviews relation to Product model**

```php
// In app/Models/Product.php add:
use Illuminate\Database\Eloquent\Relations\HasMany;

public function reviews(): HasMany
{
    return $this->hasMany(Review::class);
}

public function averageRating(): float
{
    return round($this->reviews()->avg('rating') ?? 0, 1);
}

public function reviewCount(): int
{
    return $this->reviews()->count();
}
```

- [ ] **Step 4: Add reviews relation to User model**

```php
// In app/Models/User.php add:
public function reviews()
{
    return $this->hasMany(Review::class);
}
```

- [ ] **Step 5: Run migration**

Run: `php artisan migrate`
Expected: reviews table created successfully

- [ ] **Step 6: Commit**

```bash
git add database/migrations/ app/Models/Review.php app/Models/Product.php app/Models/User.php
git commit -m "feat: add reviews table, Review model, and relations"
```

---

## Task 2: Create ReviewController

**Files:**
- Create: `app/Http/Controllers/ReviewController.php`
- Create: `app/Http/Requests/StoreReviewRequest.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create StoreReviewRequest**

Run: `php artisan make:request StoreReviewRequest`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Silakan pilih rating.',
            'rating.min' => 'Rating minimal 1 bintang.',
            'rating.max' => 'Rating maksimal 5 bintang.',
            'comment.max' => 'Komentar maksimal 1000 karakter.',
        ];
    }
}
```

- [ ] **Step 2: Create ReviewController**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Http\Requests\StoreReviewRequest;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Product $product)
    {
        $validated = $request->validated();

        Review::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]
        );

        return redirect()->route('product.detail', $product->id)
            ->with('success', 'Review berhasil disimpan!');
    }

    public function destroy(Product $product)
    {
        Review::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->delete();

        return redirect()->route('product.detail', $product->id)
            ->with('success', 'Review berhasil dihapus.');
    }
}
```

- [ ] **Step 3: Add routes**

```php
// Inside auth middleware group in web.php
Route::post('/product/{product}/review', [ReviewController::class, 'store'])->name('review.store');
Route::delete('/product/{product}/review', [ReviewController::class, 'destroy'])->name('review.destroy');
```

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/ReviewController.php app/Http/Requests/StoreReviewRequest.php routes/web.php
git commit -m "feat: add ReviewController with store and destroy actions"
```

---

## Task 3: Create Review UI Components

**Files:**
- Create: `resources/views/components/star-rating.blade.php`
- Create: `resources/views/components/review-form.blade.php`
- Create: `resources/views/components/review-list.blade.php`

- [ ] **Step 1: Create star-rating component (display only)**

```html
{{-- resources/views/components/star-rating.blade.php --}}
@props(['rating' => 0, 'size' => 'w-4 h-4'])

<div class="flex items-center gap-0.5">
    @for($i = 1; $i <= 5; $i++)
        <svg class="{{ $size }} {{ $i <= round($rating) ? 'text-brand-500' : 'text-gray-300 dark:text-gray-600' }}"
             fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
    @endfor
</div>
```

- [ ] **Step 2: Create review-form component (interactive with Alpine.js)**

```html
{{-- resources/views/components/review-form.blade.php --}}
@props(['product', 'existingReview' => null])

<div x-data="{ rating: {{ $existingReview?->rating ?? 0 }}, hoverRating: 0 }" class="bg-white dark:bg-surface-800 rounded-xl border border-gray-100 dark:border-gray-700 p-6">
    <h3 class="font-serif text-lg font-semibold text-gray-900 dark:text-white mb-4">
        {{ $existingReview ? 'Edit Review Anda' : 'Tulis Review' }}
    </h3>

    <form action="{{ route('review.store', $product->id) }}" method="POST">
        @csrf

        {{-- Star Rating Input --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating</label>
            <div class="flex items-center gap-1">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button"
                            @click="rating = {{ $i }}"
                            @mouseenter="hoverRating = {{ $i }}"
                            @mouseleave="hoverRating = 0"
                            class="focus:outline-none">
                        <svg class="w-8 h-8 transition-colors"
                             :class="(hoverRating || rating) >= {{ $i }} ? 'text-brand-500' : 'text-gray-300 dark:text-gray-600'"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </button>
                @endfor
            </div>
            <input type="hidden" name="rating" :value="rating">
            @error('rating')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Comment --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Komentar (opsional)</label>
            <textarea name="comment" rows="3"
                      class="w-full rounded-lg border-gray-200 dark:border-gray-700 dark:bg-surface-900 dark:text-white text-sm focus:ring-brand-500 focus:border-brand-500"
                      placeholder="Bagikan pengalaman Anda...">{{ $existingReview?->comment }}</textarea>
            @error('comment')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary text-sm">
            {{ $existingReview ? 'Update Review' : 'Kirim Review' }}
        </button>
    </form>
</div>
```

- [ ] **Step 3: Create review-list component**

```html
{{-- resources/views/components/review-list.blade.php --}}
@props(['reviews'])

<div class="space-y-4">
    @forelse($reviews as $review)
        <div class="bg-white dark:bg-surface-800 rounded-xl border border-gray-100 dark:border-gray-700 p-5">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center">
                        <span class="text-sm font-semibold text-brand-700 dark:text-brand-300">
                            {{ strtoupper(substr($review->user->username, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $review->user->username }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <x-star-rating :rating="$review->rating" size="w-4 h-4" />
            </div>
            @if($review->comment)
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">{{ $review->comment }}</p>
            @endif

            {{-- Delete button if own review --}}
            @if(auth()->id() === $review->user_id)
                <form action="{{ route('review.destroy', $review->product_id) }}" method="POST" class="mt-3">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700">Hapus review</button>
                </form>
            @endif
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada review untuk produk ini.</p>
    @endforelse
</div>
```

- [ ] **Step 4: Commit**

```bash
git add resources/views/components/star-rating.blade.php resources/views/components/review-form.blade.php resources/views/components/review-list.blade.php
git commit -m "feat: add star-rating, review-form, and review-list Blade components"
```

---

## Task 4: Integrate Reviews into Product Detail Page

**Files:**
- Modify: `app/Http/Controllers/UserController.php`
- Modify: `resources/views/user/detail.blade.php`

- [ ] **Step 1: Update UserController show method to load reviews**

```php
public function show($id)
{
    $product = Product::with(['reviews.user'])->findOrFail($id);
    $previous = Product::where('id', '<', $product->id)->orderBy('id', 'desc')->first();
    $next = Product::where('id', '>', $product->id)->orderBy('id', 'asc')->first();

    $existingReview = null;
    if (auth()->check()) {
        $existingReview = $product->reviews->where('user_id', auth()->id())->first();
    }

    return view('user.detail', compact('product', 'previous', 'next', 'existingReview'));
}
```

- [ ] **Step 2: Add reviews section to detail page**

After the product info section in `detail.blade.php`, add:

```html
{{-- Reviews Section --}}
<section class="mt-12">
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-serif text-2xl font-bold text-gray-900 dark:text-white">
            Review ({{ $product->reviewCount() }})
        </h2>
        <div class="flex items-center gap-2">
            <x-star-rating :rating="$product->averageRating()" size="w-5 h-5" />
            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                {{ $product->averageRating() }}/5
            </span>
        </div>
    </div>

    {{-- Review Form --}}
    @auth
        <div class="mb-8">
            <x-review-form :product="$product" :existingReview="$existingReview" />
        </div>
    @else
        <div class="bg-gray-50 dark:bg-surface-800 rounded-xl p-6 text-center mb-8">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Login untuk memberikan review</p>
            <a href="{{ route('login') }}" class="btn-primary text-sm">Login</a>
        </div>
    @endauth

    {{-- Review List --}}
    <x-review-list :reviews="$product->reviews->sortByDesc('created_at')" />
</section>
```

- [ ] **Step 3: Add average rating to product cards on home page**

In the product card on `home.blade.php`, add after category badge:

```html
@if($product->reviewCount() > 0)
    <div class="flex items-center gap-1 mb-2">
        <x-star-rating :rating="$product->averageRating()" size="w-3.5 h-3.5" />
        <span class="text-xs text-gray-500 dark:text-gray-400">({{ $product->reviewCount() }})</span>
    </div>
@endif
```

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/UserController.php resources/views/user/detail.blade.php resources/views/user/home.blade.php
git commit -m "feat: integrate reviews into product detail and home pages"
```
