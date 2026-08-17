<?php

use App\Models\Merchant;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

test('guest cannot access checkout page', function () {
    $response = $this->get('/checkout/1');

    $response->assertRedirect(route('login', absolute: false));
});

test('authenticated user can access checkout page', function () {
    $user = User::factory()->create();
    $merchant = Merchant::create([
        'name' => 'Kantin Test',
        'location' => 'Lokasi Test',
        'whatsapp' => '628123456789',
    ]);
    $product = Product::create([
        'merchant_id' => $merchant->id,
        'title' => 'Test Product',
        'original_price' => 20000,
        'discount_price' => 8000,
        'stock' => 3,
        'pickup_time' => '17:00 - 19:00 WIB',
        'image_url' => 'https://example.com/image.jpg',
    ]);

    $response = $this->actingAs($user)->get('/checkout/' . $product->id);

    $response->assertOk();
});

test('authenticated user can place an order with quantity and user association', function () {
    $user = User::factory()->create();
    $merchant = Merchant::create([
        'name' => 'Kantin Test',
        'location' => 'Lokasi Test',
        'whatsapp' => '628123456789',
    ]);
    $product = Product::create([
        'merchant_id' => $merchant->id,
        'title' => 'Test Product',
        'original_price' => 20000,
        'discount_price' => 8000,
        'stock' => 5,
        'pickup_time' => '17:00 - 19:00 WIB',
        'image_url' => 'https://example.com/image.jpg',
    ]);

    $response = $this->actingAs($user)->post('/checkout/process', [
        'product_id' => $product->id,
        'customer_name' => $user->name,
        'customer_phone' => '081234567890',
        'quantity' => 2,
    ]);

    $response->assertRedirect();
    $order = Order::query()->first();
    expect($order)->not->toBeNull()
        ->and($order->user_id)->toBe($user->id)
        ->and($order->quantity)->toBe(2)
        ->and($order->total_price)->toBe(16000);
});
