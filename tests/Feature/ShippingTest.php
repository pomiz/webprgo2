<?php

use App\Models\Location;
use App\Models\StoreSetting;
use App\Models\User;
use App\Models\UserAddress;

test('authenticated user can get provinces list', function () {
    $user = User::factory()->create(['role' => 'user']);

    Location::create(['province' => 'DKI Jakarta', 'city' => 'Jakarta Pusat', 'latitude' => -6.1862, 'longitude' => 106.8340]);
    Location::create(['province' => 'Jawa Barat', 'city' => 'Bandung', 'latitude' => -6.9175, 'longitude' => 107.6191]);

    $response = $this->actingAs($user)->get(route('shipping.provinces'));

    $response->assertStatus(200);
    $response->assertJsonCount(2);
    $response->assertJsonFragment(['DKI Jakarta']);
    $response->assertJsonFragment(['Jawa Barat']);
});

test('authenticated user can get cities by province', function () {
    $user = User::factory()->create(['role' => 'user']);

    Location::create(['province' => 'DKI Jakarta', 'city' => 'Jakarta Pusat', 'latitude' => -6.1862, 'longitude' => 106.8340]);
    Location::create(['province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'latitude' => -6.2615, 'longitude' => 106.8106]);
    Location::create(['province' => 'Jawa Barat', 'city' => 'Bandung', 'latitude' => -6.9175, 'longitude' => 107.6191]);

    $response = $this->actingAs($user)->get(route('shipping.cities', ['province' => 'DKI Jakarta']));

    $response->assertStatus(200);
    $response->assertJsonCount(2);
});

test('authenticated user can calculate shipping cost', function () {
    $user = User::factory()->create(['role' => 'user']);

    StoreSetting::create([
        'store_name' => 'Ruang Baju',
        'store_latitude' => -6.2088,
        'store_longitude' => 106.8456,
        'shipping_rate_per_km' => 2000,
        'min_shipping_cost' => 10000,
        'max_shipping_cost' => 100000,
    ]);

    $response = $this->actingAs($user)->postJson(route('shipping.calculate'), [
        'latitude' => -6.9175,
        'longitude' => 107.6191,
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['distance_km', 'cost', 'error']);
    $response->assertJson(['error' => null]);
    expect($response->json('distance_km'))->toBeGreaterThan(0);
    expect($response->json('cost'))->toBeGreaterThan(0);
});

test('shipping calculation returns error when store location not set', function () {
    $user = User::factory()->create(['role' => 'user']);

    StoreSetting::create([
        'store_name' => 'Ruang Baju',
        'store_latitude' => null,
        'store_longitude' => null,
        'shipping_rate_per_km' => 2000,
        'min_shipping_cost' => 10000,
        'max_shipping_cost' => 100000,
    ]);

    $response = $this->actingAs($user)->postJson(route('shipping.calculate'), [
        'latitude' => -6.9175,
        'longitude' => 107.6191,
    ]);

    $response->assertStatus(200);
    $response->assertJson(['error' => 'Lokasi toko belum diatur.']);
});

test('authenticated user can save address', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->postJson(route('shipping.save-address'), [
        'province' => 'DKI Jakarta',
        'city' => 'Jakarta Selatan',
        'full_address' => 'Jl. Test No. 123',
        'latitude' => -6.2615,
        'longitude' => 106.8106,
    ]);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
    $this->assertDatabaseHas('user_addresses', [
        'user_id' => $user->id,
        'province' => 'DKI Jakarta',
        'city' => 'Jakarta Selatan',
        'is_default' => true,
    ]);
});

test('unauthenticated user cannot access shipping endpoints', function () {
    $response = $this->get(route('shipping.provinces'));
    $response->assertRedirect(route('login'));
});

test('shipping calculate validates required fields', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->postJson(route('shipping.calculate'), []);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['latitude', 'longitude']);
});
