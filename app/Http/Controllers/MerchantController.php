<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MerchantController extends Controller
{
    /**
     * Helper privat untuk mengambil atau membuat profil merchant user yang sedang login.
     */
    private function getAuthenticatedMerchant(): Merchant
    {
        $user = Auth::user();

        return Merchant::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => $user->name,
                'location' => 'Kantin Baru',
                'whatsapp' => '081234567890'
            ]
        );
    }

    public function dashboard(): View
    {
        $merchant = $this->getAuthenticatedMerchant();

        $products = Product::where('merchant_id', $merchant->id)
            ->orderByDesc('created_at')
            ->get();

        $orders = Order::with(['product', 'user'])
            ->whereHas('product', function ($query) use ($merchant) {
                $query->where('merchant_id', $merchant->id);
            })
            ->orderByDesc('created_at')
            ->get();

        return view('merchant.dashboard', compact('products', 'orders'));
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'original_price'  => ['required', 'integer', 'min:1'],
            'discount_price'  => ['required', 'integer', 'min:1', 'lte:original_price'],
            'stock'           => ['required', 'integer', 'min:1'],
            'pickup_time'     => ['required', 'string', 'max:100'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'image'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ], [
            'discount_price.lte' => 'Harga diskon tidak boleh lebih mahal dari harga asli.',
        ]);

        $merchant = $this->getAuthenticatedMerchant();

        $imageUrl = 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=1200';

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $merchant->id . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('products', $filename, 'public');
            $imageUrl = '/storage/' . $path;
        }

        Product::create([
            'merchant_id'     => $merchant->id,
            'title'           => $validated['title'],
            'original_price'  => $validated['original_price'],
            'discount_price'  => $validated['discount_price'],
            'stock'           => $validated['stock'],
            'pickup_time'     => $validated['pickup_time'],
            'pickup_location' => $validated['pickup_location'],
            'image_url'       => $imageUrl,
        ]);

        return redirect()->route('merchant.dashboard')->with('status', 'Menu baru berhasil ditambahkan.');
    }

    public function redeemOrder(Request $request, Order $order): RedirectResponse
    {
        $merchant = $order->product->merchant;

        // Otorisasi: Hanya pemilik toko yang berhak melakukan klaim/redeem
        if (!$merchant || $merchant->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola pesanan ini.');
        }

        // Mencegah klaim ganda jika pesanan sudah pernah diserahkan
        if ($order->status === 'served') {
            return back()->withErrors(['code' => 'Pesanan ini sudah pernah diambil/diredeem sebelumnya.']);
        }

        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        // Pembersihan spasi & normalisasi huruf kapital pada kode booking
        $inputCode = strtoupper(trim($validated['code']));
        $targetCode = strtoupper(trim($order->booking_code));

        if ($inputCode !== $targetCode) {
            return back()->withErrors(['code' => 'Kode booking tidak sesuai dengan transaksi ini.']);
        }

        $order->update(['status' => 'served']);

        return redirect()->route('merchant.dashboard')->with('status', 'Order berhasil diredeem dan makanan siap diserahkan.');
    }
}