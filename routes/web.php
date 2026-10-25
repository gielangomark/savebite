<?php

use App\Http\Controllers\FoodController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\DuitkuPaymentController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Save Bite & Auth
|--------------------------------------------------------------------------
*/

// Halaman Utama & Detail Makanan
Route::get('/', [FoodController::class, 'index'])->name('home');
Route::get('/food/{id}', [FoodController::class, 'detail'])->name('food.detail');

// Callback Webhook Midtrans (Di luar middleware auth)
Route::post('/midtrans/callback', [FoodController::class, 'midtransCallback'])->name('midtrans.callback');
Route::post('/duitku/callback', [DuitkuPaymentController::class, 'callback'])->name('duitku.callback');

// Route khusus pengguna terautentikasi (Customer & Merchant)
Route::middleware('auth')->group(function () {
    
    // Transaksi & Tiket Customer
    Route::get('/checkout/{product}', [DuitkuPaymentController::class, 'checkout'])->name('checkout');
    Route::post('/checkout/process', [DuitkuPaymentController::class, 'process'])->name('checkout.process');
    Route::post('/orders/{order}/cancel', [DuitkuPaymentController::class, 'cancel'])->name('orders.cancel');
    Route::get('/ticket/{code}', [FoodController::class, 'ticket'])->name('ticket');

    // Riwayat Pesanan Customer
    Route::get('/history', [FoodController::class, 'history'])->name('orders.history');
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/customer/orders', [FoodController::class, 'customerOrders'])->name('customer.orders');
    Route::get('/customer/orders/status', [FoodController::class, 'customerOrderStatus'])->name('customer.orders.status');
    Route::get('/payment/{id}', [FoodController::class, 'paymentPage'])->name('payment.page');
    Route::post('/payment/sync/{bookingCode}', [FoodController::class, 'syncPaymentStatus'])->name('payment.sync');

    // Route Ulasan (Diselaraskan nama alias jamak dan tunggal agar aman di Blade)
   Route::post('/review/store', [ReviewController::class, 'store'])->name('review.store');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Pengelolaan Profil User
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route Area Merchant / Penjual (Terpusat di MerchantController)
    Route::middleware('merchant')->prefix('merchant')->name('merchant.')->group(function () {
        Route::get('/dashboard', [MerchantController::class, 'dashboard'])->name('dashboard');
        
        // CRUD Produk
        Route::post('/products', [MerchantController::class, 'storeProduct'])->name('products.store');
        Route::get('/products/{id}/edit', [MerchantController::class, 'editProduct'])->name('products.edit');
        Route::put('/products/{id}', [MerchantController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{id}', [MerchantController::class, 'destroyProduct'])->name('products.destroy');
        Route::patch('/products/{product}/availability', [MerchantController::class, 'toggleProduct'])->name('products.availability');
        Route::post('/withdrawals', [MerchantController::class, 'withdraw'])->name('withdrawals.store');

        // Lokasi Toko
        Route::get('/location', [MerchantController::class, 'editLocation'])->name('location.edit');
        Route::put('/location', [MerchantController::class, 'updateLocation'])->name('location.update');

        // Redeem / Verifikasi Pesanan
        Route::post('/orders/{order}/redeem', [MerchantController::class, 'redeemOrder'])->name('orders.redeem');
    });
});

// Route Bawaan Laravel Breeze
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
