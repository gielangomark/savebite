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
        'claim_code' => 'SB-CLAIM-1',
        'customer_name' => 'Customer Test',
        'customer_phone' => '08123456789',
        'quantity' => 2,
        'total_price' => 16000,
        'status' => 'ready_for_pickup',
    ]);

    $this->actingAs($merchant)
        ->post(route('merchant.orders.redeem', $order->id), ['code' => $order->claim_code])
        ->assertRedirect(route('merchant.dashboard'));

    $order->refresh();
    expect($order->status)->toBe('completed');
});

it('customer can fetch live order status updates and success message', function () {
    $customer = User::factory()->create(['role' => 'student']);
    $merchantUser = User::factory()->create(['role' => 'merchant']);

    $merchant = \App\Models\Merchant::create([
        'user_id' => $merchantUser->id,
        'name' => 'Kantin Uji',
        'location' => 'Lokasi Uji',
        'whatsapp' => '08123456789',
    ]);

    $product = Product::create([
        'merchant_id' => $merchant->id,
        'title' => 'Paket Uji',
        'original_price' => 20000,
        'discount_price' => 15000,
        'stock' => 20,
        'pickup_time' => '18:00 - 20:00',
        'pickup_location' => 'Depan Kantin',
        'image_url' => 'https://example.com/test.jpg',
    ]);

    $order = Order::create([
        'product_id' => $product->id,
        'user_id' => $customer->id,
        'booking_code' => 'SB-LIVE-1',
        'claim_code' => 'SB-ABC123',
        'customer_name' => 'Customer Live',
        'customer_phone' => '08111111111',
        'quantity' => 1,
        'total_price' => 15000,
        'status' => 'ready_for_pickup',
    ]);

    $this->actingAs($customer)
        ->getJson(route('customer.orders.status'))
        ->assertOk()
        ->assertJsonFragment(['status' => 'ready_for_pickup'])
        ->assertJsonFragment(['message' => 'Pesanan kamu sudah siap diambil. Yuk ambil sekarang!']);

    $order->update(['status' => 'completed']);

    $this->actingAs($customer)
        ->getJson(route('customer.orders.status'))
        ->assertOk()
        ->assertJsonFragment(['status' => 'completed'])
        ->assertJsonFragment(['message' => 'Pesanan kamu sudah berhasil diselesaikan. Terima kasih sudah memesan!']);
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
