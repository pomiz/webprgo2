# Phase 3: Fitur Ongkir (Shipping Cost Calculator)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox syntax for tracking.

**Goal:** Implement shipping cost calculation based on distance between store location and user location. Admin sets store location and tariff per km. User can pick city from dropdown OR use GPS geolocation. Distance calculated with Haversine formula.

**Architecture:** New `store_settings` table for admin config, `locations` table with Indonesian cities/coordinates, `user_addresses` table for saved user addresses. Haversine formula in a Service class. Filament resource for admin settings.

**Tech Stack:** Laravel 10, Filament 3, MySQL, Browser Geolocation API, Haversine Formula

---

## Task 1: Create Database Migrations

**Files:**
- Create: `database/migrations/xxxx_create_store_settings_table.php`
- Create: `database/migrations/xxxx_create_locations_table.php`
- Create: `database/migrations/xxxx_create_user_addresses_table.php`
- Create: `database/migrations/xxxx_add_shipping_cost_to_orders_table.php`

- [ ] **Step 1: Create store_settings migration**

Run: `php artisan make:migration create_store_settings_table`

```php
Schema::create('store_settings', function (Blueprint $table) {
    $table->id();
    $table->string('store_name')->default('Ruang Baju');
    $table->string('store_city')->nullable();
    $table->string('store_province')->nullable();
    $table->decimal('store_latitude', 10, 7)->nullable();
    $table->decimal('store_longitude', 10, 7)->nullable();
    $table->decimal('shipping_rate_per_km', 10, 2)->default(2000);
    $table->decimal('min_shipping_cost', 10, 2)->default(10000);
    $table->decimal('max_shipping_cost', 10, 2)->default(100000);
    $table->timestamps();
});
```

- [ ] **Step 2: Create locations migration**

Run: `php artisan make:migration create_locations_table`

```php
Schema::create('locations', function (Blueprint $table) {
    $table->id();
    $table->string('province');
    $table->string('city');
    $table->string('district')->nullable();
    $table->decimal('latitude', 10, 7);
    $table->decimal('longitude', 10, 7);
    $table->timestamps();
});
```

- [ ] **Step 3: Create user_addresses migration**

Run: `php artisan make:migration create_user_addresses_table`

```php
Schema::create('user_addresses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('label')->default('Rumah');
    $table->string('province')->nullable();
    $table->string('city')->nullable();
    $table->string('district')->nullable();
    $table->text('full_address')->nullable();
    $table->decimal('latitude', 10, 7)->nullable();
    $table->decimal('longitude', 10, 7)->nullable();
    $table->boolean('is_default')->default(false);
    $table->timestamps();
});
```

- [ ] **Step 4: Add shipping_cost to orders table**

Run: `php artisan make:migration add_shipping_fields_to_orders_table`

```php
Schema::table('orders', function (Blueprint $table) {
    $table->decimal('shipping_cost', 12, 2)->default(0)->after('total_price');
    $table->decimal('subtotal', 12, 2)->default(0)->after('total_price');
    $table->string('shipping_address')->nullable()->after('shipping_cost');
});
```

- [ ] **Step 5: Run migrations**

Run: `php artisan migrate`
Expected: All migrations run successfully

- [ ] **Step 6: Commit**

```bash
git add database/migrations/
git commit -m "feat: add migrations for store_settings, locations, user_addresses, and shipping fields"
```

---

## Task 2: Create Models

**Files:**
- Create: `app/Models/StoreSetting.php`
- Create: `app/Models/Location.php`
- Create: `app/Models/UserAddress.php`
- Modify: `app/Models/Order.php`
- Modify: `app/Models/User.php`

- [ ] **Step 1: Create StoreSetting model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'store_name',
        'store_city',
        'store_province',
        'store_latitude',
        'store_longitude',
        'shipping_rate_per_km',
        'min_shipping_cost',
        'max_shipping_cost',
    ];

    protected $casts = [
        'store_latitude' => 'decimal:7',
        'store_longitude' => 'decimal:7',
        'shipping_rate_per_km' => 'decimal:2',
        'min_shipping_cost' => 'decimal:2',
        'max_shipping_cost' => 'decimal:2',
    ];

    public static function get(): self
    {
        return self::firstOrCreate([], [
            'store_name' => 'Ruang Baju',
            'shipping_rate_per_km' => 2000,
            'min_shipping_cost' => 10000,
            'max_shipping_cost' => 100000,
        ]);
    }
}
```

- [ ] **Step 2: Create Location model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['province', 'city', 'district', 'latitude', 'longitude'];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];
}
```

- [ ] **Step 3: Create UserAddress model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id', 'label', 'province', 'city', 'district',
        'full_address', 'latitude', 'longitude', 'is_default',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

- [ ] **Step 4: Update Order model with new fields**

Add to `$fillable`: `'subtotal', 'shipping_cost', 'shipping_address'`

- [ ] **Step 5: Add addresses relation to User model**

```php
public function addresses()
{
    return $this->hasMany(UserAddress::class);
}
```

- [ ] **Step 6: Commit**

```bash
git add app/Models/
git commit -m "feat: add StoreSetting, Location, UserAddress models and update Order/User"
```

---

## Task 3: Create Shipping Service (Haversine)

**Files:**
- Create: `app/Services/ShippingService.php`

- [ ] **Step 1: Create ShippingService with Haversine calculation**

```php
<?php

namespace App\Services;

use App\Models\StoreSetting;

class ShippingService
{
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function calculateShippingCost(float $userLat, float $userLon): array
    {
        $settings = StoreSetting::get();

        if (!$settings->store_latitude || !$settings->store_longitude) {
            return [
                'distance_km' => 0,
                'cost' => 0,
                'error' => 'Lokasi toko belum diatur.',
            ];
        }

        $distance = $this->calculateDistance(
            $settings->store_latitude,
            $settings->store_longitude,
            $userLat,
            $userLon
        );

        $cost = $distance * $settings->shipping_rate_per_km;

        // Apply min/max bounds
        $cost = max($settings->min_shipping_cost, $cost);
        $cost = min($settings->max_shipping_cost, $cost);

        return [
            'distance_km' => round($distance, 2),
            'cost' => round($cost, 0),
            'error' => null,
        ];
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Services/ShippingService.php
git commit -m "feat: add ShippingService with Haversine distance calculation"
```

---

## Task 4: Create Location Seeder

**Files:**
- Create: `database/seeders/LocationSeeder.php`

- [ ] **Step 1: Create seeder with major Indonesian cities**

Include at least 30-50 major cities with their coordinates (provinsi, kota, latitude, longitude). Examples:
- Jakarta (-6.2088, 106.8456)
- Bandung (-6.9175, 107.6191)
- Surabaya (-7.2575, 112.7521)
- Yogyakarta (-7.7956, 110.3695)
- Semarang (-6.9666, 110.4196)
- Medan (3.5952, 98.6722)
- Makassar (-5.1477, 119.4327)
- etc.

- [ ] **Step 2: Run seeder**

Run: `php artisan db:seed --class=LocationSeeder`

- [ ] **Step 3: Commit**

```bash
git add database/seeders/LocationSeeder.php
git commit -m "feat: add LocationSeeder with Indonesian cities coordinates"
```

---

## Task 5: Admin Panel - Store Settings Page

**Files:**
- Create: `app/Filament/Pages/StoreSettings.php`

- [ ] **Step 1: Create Filament page for store settings**

Filament Page (not Resource) with form:
- Store name (text input)
- Province dropdown (populated from locations table)
- City dropdown (filtered by province, auto-fills coordinates)
- Manual latitude/longitude inputs (auto-filled from city selection)
- Shipping rate per km (number input, prefix Rp)
- Min shipping cost (number input)
- Max shipping cost (number input)
- Save button

- [ ] **Step 2: Commit**

```bash
git add app/Filament/Pages/StoreSettings.php
git commit -m "feat: add Filament store settings page for shipping config"
```

---

## Task 6: Shipping API Endpoint

**Files:**
- Create: `app/Http/Controllers/ShippingController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create ShippingController**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\UserAddress;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function getProvinces()
    {
        $provinces = Location::select('province')->distinct()->orderBy('province')->pluck('province');
        return response()->json($provinces);
    }

    public function getCities(Request $request)
    {
        $cities = Location::where('province', $request->province)
            ->select('city', 'latitude', 'longitude')
            ->distinct()
            ->orderBy('city')
            ->get();
        return response()->json($cities);
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $service = new ShippingService();
        $result = $service->calculateShippingCost(
            $request->latitude,
            $request->longitude
        );

        return response()->json($result);
    }

    public function saveAddress(Request $request)
    {
        $request->validate([
            'province' => 'nullable|string',
            'city' => 'nullable|string',
            'full_address' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $address = UserAddress::updateOrCreate(
            ['user_id' => auth()->id(), 'is_default' => true],
            [
                'province' => $request->province,
                'city' => $request->city,
                'full_address' => $request->full_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'is_default' => true,
            ]
        );

        return response()->json(['success' => true, 'address' => $address]);
    }
}
```

- [ ] **Step 2: Add routes**

```php
// Inside auth middleware group in web.php
Route::get('/shipping/provinces', [ShippingController::class, 'getProvinces'])->name('shipping.provinces');
Route::get('/shipping/cities', [ShippingController::class, 'getCities'])->name('shipping.cities');
Route::post('/shipping/calculate', [ShippingController::class, 'calculate'])->name('shipping.calculate');
Route::post('/shipping/save-address', [ShippingController::class, 'saveAddress'])->name('shipping.save-address');
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/ShippingController.php routes/web.php
git commit -m "feat: add shipping API endpoints for provinces, cities, and cost calculation"
```

---

## Task 7: Integrate Shipping into Checkout Flow

**Files:**
- Modify: `app/Http/Controllers/CheckoutController.php`
- Create: `resources/views/checkout/index.blade.php` (checkout page before confirmation)

- [ ] **Step 1: Create checkout page view**

New page between cart and invoice that shows:
- Order summary (items, subtotal)
- Address selection section:
  - Dropdown: Province > City (cascading, fetched via AJAX)
  - OR button "Gunakan Lokasi Saya" (browser geolocation)
  - Display selected address and calculated distance
- Shipping cost display (calculated via AJAX call to /shipping/calculate)
- Total (subtotal + shipping)
- Confirm order button

- [ ] **Step 2: Update CheckoutController**

Add `showCheckout()` method that displays the checkout page.
Modify `checkout()` to accept shipping_cost and address data, save to order.

- [ ] **Step 3: Add route for checkout page**

```php
Route::get('/checkout', [CheckoutController::class, 'showCheckout'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout.process');
```

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/CheckoutController.php resources/views/checkout/index.blade.php routes/web.php
git commit -m "feat: integrate shipping cost calculation into checkout flow"
```
