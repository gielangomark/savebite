<?php

use App\Models\Merchant;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;

test('logged-in customers see every food ordered by rating then distance', function () {
    $customer = User::factory()->create();
    $reviewer = User::factory()->create();

    $nearMerchant = Merchant::create([
        'name' => 'Kantin Dekat', 'location' => 'Kampus', 'whatsapp' => '628111111111',
        'latitude' => -6.20000000, 'longitude' => 106.81600000,
    ]);
    $farMerchant = Merchant::create([
        'name' => 'Kantin Jauh', 'location' => 'Kampus', 'whatsapp' => '628222222222',
        'latitude' => -6.22000000, 'longitude' => 106.81600000,
    ]);

    $nearFood = createRecommendationProduct($nearMerchant, 'Menu Bintang Lima Dekat');
    $farFood = createRecommendationProduct($farMerchant, 'Menu Bintang Lima Jauh');
    $notFiveStarFood = createRecommendationProduct($nearMerchant, 'Menu Empat Bintang');

    addRecommendationReview($nearFood, $reviewer, 5);
    addRecommendationReview($farFood, $reviewer, 5);
    addRecommendationReview($notFiveStarFood, $reviewer, 4);

    $response = $this->actingAs($customer)->get('/?lat=-6.2000&lon=106.8160');

    $response->assertOk()
        ->assertSeeInOrder(['Menu Bintang Lima Dekat', 'Menu Bintang Lima Jauh', 'Menu Empat Bintang'])
        ->assertSee('Rekomendasi Makanan untuk Anda');
});

test('customers still see lower-rated foods when no five-star food exists', function () {
    $customer = User::factory()->create();
    $reviewer = User::factory()->create();
    $merchant = Merchant::create([
        'name' => 'Kantin Rating', 'location' => 'Kampus', 'whatsapp' => '628333333333',
        'latitude' => -6.20000000, 'longitude' => 106.81600000,
    ]);

    $bestFood = createRecommendationProduct($merchant, 'Menu Rating Terbaik');
    $lowerFood = createRecommendationProduct($merchant, 'Menu Rating Lebih Rendah');
    addRecommendationReview($bestFood, $reviewer, 4);
    addRecommendationReview($lowerFood, $reviewer, 3);

    $response = $this->actingAs($customer)->get('/?lat=-6.2000&lon=106.8160');

    $response->assertOk()
        ->assertSee('Rekomendasi Makanan untuk Anda')
        ->assertSee('Menu Rating Terbaik')
        ->assertSee('Menu Rating Lebih Rendah');
});

function createRecommendationProduct(Merchant $merchant, string $title): Product
{
    return Product::create([
        'merchant_id' => $merchant->id, 'title' => $title,
        'original_price' => 20000, 'discount_price' => 10000, 'stock' => 2,
        'pickup_time' => '17:00', 'image_url' => 'https://example.com/food.jpg',
    ]);
}

function addRecommendationReview(Product $product, User $reviewer, int $rating): void
{
    $order = Order::create([
        'user_id' => $reviewer->id,
        'product_id' => $product->id,
        'booking_code' => 'SB-TEST-' . strtoupper(uniqid()),
        'customer_name' => $reviewer->name,
        'customer_phone' => '081234567890',
        'quantity' => 1,
        'total_price' => $product->discount_price,
        'status' => 'completed',
    ]);

    Review::create([
        'order_id' => $order->id,
        'user_id' => $reviewer->id,
        'product_id' => $product->id,
        'rating' => $rating,
    ]);
}
