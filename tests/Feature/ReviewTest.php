<?php

use App\Models\Product;
use App\Models\Review;
use App\Models\User;

test('authenticated user can submit a review', function () {
    $user = User::factory()->create(['role' => 'user']);
    $product = Product::create([
        'name' => 'Test Product',
        'description' => 'Test description',
        'category' => 'Kaos',
        'price' => 100000,
        'stock' => 10,
        'image' => 'test.jpg',
    ]);

    $response = $this->actingAs($user)->post(route('review.store', $product), [
        'rating' => 5,
        'comment' => 'Produk bagus sekali!',
    ]);

    $response->assertRedirect(route('product.detail', $product->id));
    $this->assertDatabaseHas('reviews', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'rating' => 5,
        'comment' => 'Produk bagus sekali!',
    ]);
});

test('authenticated user can submit review without comment', function () {
    $user = User::factory()->create(['role' => 'user']);
    $product = Product::create([
        'name' => 'Test Product',
        'description' => 'Test',
        'category' => 'Kaos',
        'price' => 50000,
        'stock' => 5,
        'image' => 'test.jpg',
    ]);

    $response = $this->actingAs($user)->post(route('review.store', $product), [
        'rating' => 4,
        'comment' => null,
    ]);

    $response->assertRedirect(route('product.detail', $product->id));
    $this->assertDatabaseHas('reviews', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'rating' => 4,
        'comment' => null,
    ]);
});

test('user can update their existing review', function () {
    $user = User::factory()->create(['role' => 'user']);
    $product = Product::create([
        'name' => 'Test Product',
        'description' => 'Test',
        'category' => 'Kaos',
        'price' => 50000,
        'stock' => 5,
        'image' => 'test.jpg',
    ]);

    // First review
    Review::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'rating' => 3,
        'comment' => 'Biasa saja',
    ]);

    // Update review
    $response = $this->actingAs($user)->post(route('review.store', $product), [
        'rating' => 5,
        'comment' => 'Ternyata bagus!',
    ]);

    $response->assertRedirect(route('product.detail', $product->id));
    $this->assertDatabaseCount('reviews', 1);
    $this->assertDatabaseHas('reviews', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'rating' => 5,
        'comment' => 'Ternyata bagus!',
    ]);
});

test('user can delete their review', function () {
    $user = User::factory()->create(['role' => 'user']);
    $product = Product::create([
        'name' => 'Test Product',
        'description' => 'Test',
        'category' => 'Kaos',
        'price' => 50000,
        'stock' => 5,
        'image' => 'test.jpg',
    ]);

    Review::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'rating' => 4,
        'comment' => 'Good',
    ]);

    $response = $this->actingAs($user)->delete(route('review.destroy', $product));

    $response->assertRedirect(route('product.detail', $product->id));
    $this->assertDatabaseMissing('reviews', [
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);
});

test('review requires rating', function () {
    $user = User::factory()->create(['role' => 'user']);
    $product = Product::create([
        'name' => 'Test Product',
        'description' => 'Test',
        'category' => 'Kaos',
        'price' => 50000,
        'stock' => 5,
        'image' => 'test.jpg',
    ]);

    $response = $this->actingAs($user)->post(route('review.store', $product), [
        'rating' => null,
        'comment' => 'No rating',
    ]);

    $response->assertSessionHasErrors('rating');
});

test('review rating must be between 1 and 5', function () {
    $user = User::factory()->create(['role' => 'user']);
    $product = Product::create([
        'name' => 'Test Product',
        'description' => 'Test',
        'category' => 'Kaos',
        'price' => 50000,
        'stock' => 5,
        'image' => 'test.jpg',
    ]);

    $response = $this->actingAs($user)->post(route('review.store', $product), [
        'rating' => 6,
        'comment' => 'Invalid rating',
    ]);

    $response->assertSessionHasErrors('rating');
});

test('unauthenticated user cannot submit review', function () {
    $product = Product::create([
        'name' => 'Test Product',
        'description' => 'Test',
        'category' => 'Kaos',
        'price' => 50000,
        'stock' => 5,
        'image' => 'test.jpg',
    ]);

    $response = $this->post(route('review.store', $product), [
        'rating' => 5,
        'comment' => 'Great!',
    ]);

    $response->assertRedirect(route('login'));
});
