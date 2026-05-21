<?php

use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('StoreSetting::get() creates default settings if none exist', function () {
    $settings = StoreSetting::get();

    expect($settings)->toBeInstanceOf(StoreSetting::class);
    expect($settings->store_name)->toBe('Ruang Baju');
    expect((float) $settings->shipping_rate_per_km)->toBe(2000.00);
    expect((float) $settings->min_shipping_cost)->toBe(10000.00);
    expect((float) $settings->max_shipping_cost)->toBe(100000.00);
});

test('StoreSetting::get() returns existing settings', function () {
    StoreSetting::create([
        'store_name' => 'Toko Custom',
        'shipping_rate_per_km' => 3000,
        'min_shipping_cost' => 15000,
        'max_shipping_cost' => 200000,
    ]);

    $settings = StoreSetting::get();

    expect($settings->store_name)->toBe('Toko Custom');
    expect((float) $settings->shipping_rate_per_km)->toBe(3000.00);
});
