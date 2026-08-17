<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Merchant;
use App\Models\Product;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $m1 = Merchant::create([
            'name' => 'Kantin Bu Siti',
            'location' => 'Gedung Rektorat Lt. 1',
            'whatsapp' => '6281234567890'
        ]);

        $m2 = Merchant::create([
            'name' => 'Bakery Kampus',
            'location' => 'Samping Perpustakaan Utama',
            'whatsapp' => '6289876543210'
        ]);

        Product::create([
            'merchant_id' => $m1->id,
            'title' => 'Nasi Ayam Goreng Komplit',
            'original_price' => 20000,
            'discount_price' => 8000,
            'stock' => 3,
            'pickup_time' => '17:00 - 19:00 WIB',
            'pickup_location' => 'Gedung Rektorat Lt. 1',
            'image_url' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=500'
        ]);

        Product::create([
            'merchant_id' => $m2->id,
            'title' => 'Roti Cokelat Keju (Pack 3 Pcs)',
            'original_price' => 18000,
            'discount_price' => 7000,
            'stock' => 5,
            'pickup_time' => '18:00 - 20:00 WIB',
            'pickup_location' => 'Samping Perpustakaan Utama',
            'image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=500'
        ]);
    }
}