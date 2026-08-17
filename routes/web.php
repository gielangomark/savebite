<?php

use App\Http\Controllers\FoodController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SurplusBite & Auth
|--------------------------------------------------------------------------
*/

// Halaman Utama & Detail Makanan (dinamis dari database)
Route::get('/', [FoodController::class, 'index'])->name('home');
Route::get('/food/{id}', [FoodController::class, 'detail'])->name('food.detail');

Route::middleware('auth')->group(function () {
    Route::get('/checkout/{id}', [FoodController::class, 'checkoutPage'])->name('checkout');
    Route::post('/checkout/process', [FoodController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/ticket/{code}', [FoodController::class, 'ticket'])->name('ticket');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route Dashboard Penjual (Mitra)
Route::middleware(['auth'])->group(function () {
    Route::get('/merchant/dashboard', [MerchantController::class, 'dashboard'])->name('merchant.dashboard');
    Route::post('/merchant/products', [MerchantController::class, 'storeProduct'])->name('merchant.products.store');
    Route::post('/merchant/orders/{order}/redeem', [MerchantController::class, 'redeemOrder'])->name('merchant.orders.redeem');
});

// Route Bawaan Laravel Breeze
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';