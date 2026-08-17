<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FoodController extends Controller
{
    public function index()
    {
        $products = Product::with('merchant')->where('stock', '>', 0)->get();
        return view('index', compact('products'));
    }

    public function detail(string|int $id)
    {
        $product = Product::with('merchant')->findOrFail($id);
        return view('detail', compact('product'));
    }

    public function checkoutPage(string|int $id)
    {
        $product = Product::with('merchant')->findOrFail($id);
        return view('checkout', compact('product'));
    }

    public function processCheckout(Request $request)
    {
        $validated = $request->validate([
            'product_id'     => ['required', 'integer', 'exists:products,id'],
            'customer_name'  => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'quantity'       => ['required', 'integer', 'min:1'],
        ]);

        try {
            $bookingCode = DB::transaction(function () use ($validated) {
                // Lock record produk untuk mencegah race condition / stok minus
                $product = Product::lockForUpdate()->findOrFail($validated['product_id']);
                $quantity = (int) $validated['quantity'];

                if ($product->stock < $quantity) {
                    throw new \Exception('Stok makanan tidak cukup untuk jumlah pesanan Anda.');
                }

                // Generasi kode booking 8 karakter agar lebih aman dari bentrokan
                $bookingCode = 'SB-' . strtoupper(Str::random(8));
                $totalPrice  = $product->discount_price * $quantity;

                Order::create([
                    'user_id'        => Auth::id(),
                    'product_id'     => $product->id,
                    'booking_code'   => $bookingCode,
                    'customer_name'  => $validated['customer_name'],
                    'customer_phone' => $validated['customer_phone'],
                    'quantity'       => $quantity,
                    'total_price'    => $totalPrice,
                    'status'         => 'pending',
                ]);

                $product->decrement('stock', $quantity);

                return $bookingCode;
            });

            return redirect()->route('ticket', ['code' => $bookingCode]);
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', $e->getMessage());
        }
    }

    public function ticket(string $code)
    {
        $order = Order::with('product.merchant')->where('booking_code', $code)->firstOrFail();

        // Otorisasi: Mencegah pengguna lain mengintip tiket milik orang lain
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        return view('ticket', compact('order'));
    }
}