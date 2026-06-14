<?php

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;

test('authenticated user can add product to cart', function () {
    $user = User::factory()->create(['role' => 'user']);
    $product = Product::create([
        'name' => 'Kaos Polos',
        'description' => 'Kaos polos nyaman',
        'category' => 'Kaos',
        'price' => 75000,
        'stock' => 10,
        'image' => 'test.jpg',
    ]);

    $response = $this->actingAs($user)->post(route('cart.add', $product->id), [
        'quantity' => 2,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

test('adding same product increments quantity', function () {
    $user = User::factory()->create(['role' => 'user']);
    $product = Product::create([
        'name' => 'Kaos Polos',
        'description' => 'Kaos polos nyaman',
        'category' => 'Kaos',
        'price' => 75000,
        'stock' => 10,
        'image' => 'test.jpg',
    ]);

    $this->actingAs($user)->post(route('cart.add', $product->id), ['quantity' => 1]);
    $this->actingAs($user)->post(route('cart.add', $product->id), ['quantity' => 2]);

    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 3,
    ]);
    $this->assertDatabaseCount('cart_items', 1);
});

test('user can remove product from cart', function () {
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

    $response = $this->actingAs($user)->delete(route('cart.remove', $product->id));

    $response->assertRedirect();
    $this->assertDatabaseMissing('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);
});

test('user can view cart page', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->get(route('cart.index'));

    $response->assertStatus(200);
});

test('unauthenticated user cannot access cart', function () {
    $response = $this->get(route('cart.index'));

    $response->assertRedirect(route('login'));
});
