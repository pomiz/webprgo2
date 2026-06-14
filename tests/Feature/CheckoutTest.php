<?php

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\User;

test('user can prepare checkout with selected products', function () {
    $user = User::factory()->create(['role' => 'user']);
    $product = Product::create([
        'name' => 'Kaos Polos',
        'description' => 'Test',
        'category' => 'Kaos',
        'price' => 75000,
        'stock' => 10,
        'image' => 'test.jpg',
    ]);

    CartItem::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->post(route('checkout.prepare'), [
        'selected_products' => [$product->id],
    ]);

    $response->assertRedirect(route('checkout.index'));
});

test('checkout prepare requires selected products', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->post(route('checkout.prepare'), [
        'selected_products' => [],
    ]);

    $response->assertSessionHasErrors('selected_products');
});

test('user can view checkout page after prepare', function () {
    $user = User::factory()->create(['role' => 'user']);
    $product = Product::create([
        'name' => 'Kaos Polos',
        'description' => 'Test',
        'category' => 'Kaos',
        'price' => 75000,
        'stock' => 10,
        'image' => 'test.jpg',
    ]);

    CartItem::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $this->actingAs($user)->post(route('checkout.prepare'), [
        'selected_products' => [$product->id],
    ]);

    $response = $this->actingAs($user)->get(route('checkout.index'));

    $response->assertStatus(200);
});

test('user can complete checkout and order is created', function () {
    $user = User::factory()->create(['role' => 'user']);
    $product = Product::create([
        'name' => 'Kaos Polos',
        'description' => 'Test',
        'category' => 'Kaos',
        'price' => 75000,
        'stock' => 10,
        'image' => 'test.jpg',
    ]);

    StoreSetting::create([
        'store_name' => 'Ruang Baju',
        'store_latitude' => -6.2088,
        'store_longitude' => 106.8456,
        'shipping_rate_per_km' => 2000,
        'min_shipping_cost' => 10000,
        'max_shipping_cost' => 100000,
    ]);

    CartItem::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    // Prepare checkout
    $this->actingAs($user)->post(route('checkout.prepare'), [
        'selected_products' => [$product->id],
    ]);

    // Process checkout
    $response = $this->actingAs($user)->post(route('checkout.process'), [
        'shipping_cost' => 15000,
        'shipping_address' => 'Jakarta Selatan',
        'latitude' => -6.2615,
        'longitude' => 106.8106,
        'province' => 'DKI Jakarta',
        'city' => 'Jakarta Selatan',
    ]);

    $order = Order::where('user_id', $user->id)->first();
    expect($order)->not->toBeNull();
    expect((float) $order->subtotal)->toBe(150000.00);
    // Shipping cost is calculated server-side from coordinates
    expect((float) $order->shipping_cost)->toBeGreaterThan(0);
    expect((float) $order->total_price)->toBe((float) $order->subtotal + (float) $order->shipping_cost);
    expect($order->status)->toBe('pending_payment');

    // Cart should be empty
    $this->assertDatabaseMissing('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);

    // Stock should be decremented
    $product->refresh();
    expect($product->stock)->toBe(8);

    $response->assertRedirect(route('invoice.show', $order));
});

test('checkout fails if stock insufficient', function () {
    $user = User::factory()->create(['role' => 'user']);
    $product = Product::create([
        'name' => 'Kaos Polos',
        'description' => 'Test',
        'category' => 'Kaos',
        'price' => 75000,
        'stock' => 1,
        'image' => 'test.jpg',
    ]);

    CartItem::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    $this->actingAs($user)->post(route('checkout.prepare'), [
        'selected_products' => [$product->id],
    ]);

    $response = $this->actingAs($user)->post(route('checkout.process'), [
        'shipping_cost' => 10000,
        'shipping_address' => 'Jakarta',
        'latitude' => -6.2088,
        'longitude' => 106.8456,
    ]);

    $response->assertRedirect(route('cart.index'));
    $response->assertSessionHas('error');
    $this->assertDatabaseCount('orders', 0);
});

test('user can view their own invoice', function () {
    $user = User::factory()->create(['role' => 'user']);
    $order = Order::create([
        'user_id' => $user->id,
        'total_price' => 100000,
        'subtotal' => 85000,
        'shipping_cost' => 15000,
        'status' => 'pending',
        'virtual_account' => 'VA20260521TESTTEST',
    ]);

    $response = $this->actingAs($user)->get(route('invoice.show', $order));

    $response->assertStatus(200);
});

test('user cannot view another users invoice', function () {
    $user1 = User::factory()->create(['role' => 'user']);
    $user2 = User::factory()->create(['role' => 'user']);
    $order = Order::create([
        'user_id' => $user1->id,
        'total_price' => 100000,
        'subtotal' => 85000,
        'shipping_cost' => 15000,
        'status' => 'pending',
        'virtual_account' => 'VA20260521TESTTEST',
    ]);

    $response = $this->actingAs($user2)->get(route('invoice.show', $order));

    $response->assertStatus(403);
});
