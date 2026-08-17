<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('merchant can access dashboard', function () {
    $merchant = User::factory()->create([
        'role' => 'merchant',
        'name' => 'Merchant Test',
    ]);

    $response = $this->actingAs($merchant)->get(route('merchant.dashboard'));

    $response->assertOk();
});

it('merchant can add a menu item and redeem an order', function () {
    $merchant = User::factory()->create(['role' => 'merchant']);

    $response = $this->actingAs($merchant)->post(route('merchant.products.store'), [
        'title' => 'Menu Baru',
        'original_price' => 20000,
        'discount_price' => 8000,
        'stock' => 12,
        'pickup_time' => '18:00 - 20:00 WIB',
        'pickup_location' => 'Depan Kantin Utama',
    ]);

    $response->assertRedirect(route('merchant.dashboard'));
    $this->assertDatabaseHas('products', ['title' => 'Menu Baru']);

    $product = Product::where('title', 'Menu Baru')->first();

    $order = Order::create([
        'product_id' => $product->id,
        'user_id' => User::factory()->create(['role' => 'student'])->id,
        'booking_code' => 'SB-TEST1',
        'customer_name' => 'Customer Test',
        'customer_phone' => '08123456789',
        'quantity' => 2,
        'total_price' => 16000,
        'status' => 'pending',
    ]);

    $this->actingAs($merchant)
        ->post(route('merchant.orders.redeem', $order->id), ['code' => $order->booking_code])
        ->assertRedirect(route('merchant.dashboard'));

    $order->refresh();
    expect($order->status)->toBe('served');
});

it('merchant can add a menu item with image upload', function () {
    Storage::fake('public');
    $merchant = User::factory()->create(['role' => 'merchant']);
    
    $response = $this->actingAs($merchant)->post(route('merchant.products.store'), [
        'title' => 'Menu dengan Foto',
        'original_price' => 25000,
        'discount_price' => 9000,
        'stock' => 15,
        'pickup_time' => '16:00 - 18:00 WIB',
        'pickup_location' => 'Samping Kantin',
        'image' => UploadedFile::fake()->image('makanan.jpg', 400, 300),
    ]);

    $response->assertRedirect(route('merchant.dashboard'));
    $this->assertDatabaseHas('products', ['title' => 'Menu dengan Foto']);
    
    $product = Product::where('title', 'Menu dengan Foto')->first();
    expect($product->stock)->toBe(15);
    expect($product->image_url)->toContain('/storage/');
});
