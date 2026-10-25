<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Withdrawal;
use App\Services\WalletService;
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
                'whatsapp' => $user->phone ?? '6281234567890'
            ]
        );
    }

    /**
     * Dashboard Merchant: Menampilkan Produk & Daftar Pesanan Masuk
     */
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

        $wallet = app(WalletService::class)->walletFor($merchant);
        $todayIncome = $orders->whereIn('status', ['completed', 'served'])->sum('merchant_amount');
        $withdrawals = $merchant->withdrawals()->latest()->take(5)->get();
        return view('merchant.dashboard', compact('products', 'orders', 'merchant', 'wallet', 'todayIncome', 'withdrawals'));
    }

    /**
     * Tambah Menu / Makanan Baru
     */
    public function storeProduct(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'original_price'  => ['required', 'integer', 'min:1'],
            'discount_price'  => ['required', 'integer', 'min:1', 'lte:original_price'],
            'stock'           => ['required', 'integer', 'min:1'],
            'pickup_start_time' => ['nullable', 'date_format:H:i','required_without:pickup_time'],
            'pickup_end_time'   => ['nullable', 'date_format:H:i'],
            'pickup_time'       => ['nullable','string','required_without:pickup_start_time'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'image'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
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

        $start = $validated['pickup_start_time'] ?? substr($validated['pickup_time'], 0, 5);
        $end = $validated['pickup_end_time'] ?? (preg_match('/-\s*(\d{2}:\d{2})/', $validated['pickup_time'], $match) ? $match[1] : '23:59');
        if ($end <= $start) return back()->withErrors(['pickup_end_time' => 'Jam selesai harus setelah jam mulai.'])->withInput();
        Product::create([
            'merchant_id'     => $merchant->id,
            'title'           => $validated['title'],
            'original_price'  => $validated['original_price'],
            'discount_price'  => $validated['discount_price'],
            'stock'           => $validated['stock'],
            'pickup_time'     => $start.' - '.$end,
            'pickup_start_time' => $start,
            'pickup_end_time' => $end,
            'pickup_location' => $validated['pickup_location'],
            'image_url'       => $imageUrl,
        ]);

        return redirect()->route('merchant.dashboard')->with('status', 'Menu baru berhasil ditambahkan.');
    }

    /**
     * Tampilkan Halaman Form Edit Produk
     */
    public function editProduct($id): View
    {
        $merchant = $this->getAuthenticatedMerchant();

        $product = Product::where('id', $id)
            ->where('merchant_id', $merchant->id)
            ->firstOrFail();

        return view('merchant.edit-product', compact('product'));
    }

    /**
     * Update Data & Foto Produk Makanan
     */
    public function updateProduct(Request $request, $id): RedirectResponse
    {
        $merchant = $this->getAuthenticatedMerchant();

        $product = Product::where('id', $id)
            ->where('merchant_id', $merchant->id)
            ->firstOrFail();

        $validated = $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'original_price'  => ['required', 'integer', 'min:1'],
            'discount_price'  => ['required', 'integer', 'min:1', 'lte:original_price'],
            'stock'           => ['required', 'integer', 'min:0'],
            'pickup_start_time' => ['nullable', 'date_format:H:i','required_without:pickup_time'],
            'pickup_end_time'   => ['nullable', 'date_format:H:i'],
            'pickup_time'       => ['nullable','string','required_without:pickup_start_time'],
            'pickup_location' => ['required', 'string', 'max:255'],
            'image'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ], [
            'discount_price.lte' => 'Harga diskon tidak boleh lebih mahal dari harga asli.',
        ]);

        // File upload bukan kolom database; hanya gunakan untuk membuat image_url.
        unset($validated['image']);
        $start = $validated['pickup_start_time'] ?? substr($validated['pickup_time'], 0, 5);
        $end = $validated['pickup_end_time'] ?? (preg_match('/-\s*(\d{2}:\d{2})/', $validated['pickup_time'], $match) ? $match[1] : '23:59');
        if ($end <= $start) return back()->withErrors(['pickup_end_time' => 'Jam selesai harus setelah jam mulai.'])->withInput();
        $validated['pickup_time'] = $start.' - '.$end;
        $validated['pickup_start_time'] = $start;
        $validated['pickup_end_time'] = $end;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = $merchant->id . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('products', $filename, 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        $product->update($validated);

        return redirect()->route('merchant.dashboard')->with('status', 'Data produk berhasil diperbarui.');
    }

    /**
     * Hapus Produk Makanan
     */
    public function destroyProduct($id): RedirectResponse
    {
        $merchant = $this->getAuthenticatedMerchant();

        $product = Product::where('id', $id)
            ->where('merchant_id', $merchant->id)
            ->firstOrFail();

        $product->delete();

        return redirect()->route('merchant.dashboard')->with('status', 'Menu berhasil dihapus.');
    }

    /**
     * Halaman Edit Lokasi Merchant
     */
    public function editLocation(): View
    {
        $merchant = $this->getAuthenticatedMerchant();
        return view('merchant.edit-location', compact('merchant'));
    }

    /**
     * Simpan Lokasi Merchant (Latitude & Longitude)
     */
    public function updateLocation(Request $request): RedirectResponse
    {
        $merchant = $this->getAuthenticatedMerchant();

        $validated = $request->validate([
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $merchant->update($validated);
        $merchant->update(['timezone' => $validated['longitude'] >= 129 ? 'Asia/Jayapura' : ($validated['longitude'] >= 117 ? 'Asia/Makassar' : 'Asia/Jakarta')]);

        return redirect()->route('merchant.dashboard')->with('status', 'Lokasi toko berhasil diperbarui!');
    }

    /**
     * Redeem / Verifikasi Penyerahan Makanan via Kode Klaim Pembeli
     */
    public function redeemOrder(Request $request, Order $order): RedirectResponse
    {
        $merchant = $this->getAuthenticatedMerchant();

        if (($order->product->merchant_id ?? null) !== $merchant->id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola pesanan ini.');
        }

        if (in_array(strtolower($order->status), ['pending', 'unpaid'])) {
            return back()->withErrors(['code' => 'Pesanan ini belum dibayar oleh pembeli.']);
        }

        if (in_array(strtolower($order->status), ['completed', 'served', 'redeemed', 'selesai'])) {
            return back()->withErrors(['code' => 'Pesanan ini sudah pernah diambil/diredeem sebelumnya.']);
        }

        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $inputCode   = strtoupper(trim($validated['code']));
        $claimCode   = strtoupper(trim($order->claim_code ?? ''));
        $bookingCode = strtoupper(trim($order->booking_code ?? ''));

        if ($inputCode !== $claimCode && $inputCode !== $bookingCode) {
            return back()->withErrors(['code' => 'Kode klaim tidak sesuai dengan transaksi ini.']);
        }

        $order->update(['status' => 'completed']);
        app(WalletService::class)->credit($merchant, (int) $order->merchant_amount, 'merchant-income-'.$order->id, 'Pendapatan pesanan '.$order->booking_code, $order);

        return redirect()->route('merchant.dashboard')->with('status', 'Orderan Anda berhasil! Selamat menikmati hidangannya.');
    }

    public function toggleProduct(Product $product): RedirectResponse
    {
        $merchant = $this->getAuthenticatedMerchant(); abort_unless($product->merchant_id === $merchant->id, 403);
        $product->update(['is_active' => ! $product->is_active]);
        return back()->with('status', $product->is_active ? 'Menu diaktifkan.' : 'Menu dinonaktifkan.');
    }

    public function withdraw(Request $request, WalletService $wallet): RedirectResponse
    {
        $merchant=$this->getAuthenticatedMerchant(); $data=$request->validate(['amount'=>['required','integer','min:50000'],'bank_code'=>['required','string','max:20'],'bank_account_number'=>['required','string','max:50'],'bank_account_name'=>['required','string','max:100']]);
        $fee=5000; if($data['amount'] <= $fee) return back()->withErrors(['amount'=>'Nominal harus lebih besar dari biaya admin.']);
        $merchant->update(['bank_code'=>$data['bank_code'],'bank_account_number'=>$data['bank_account_number'],'bank_account_name'=>$data['bank_account_name']]);
        $withdrawal=Withdrawal::create(['merchant_id'=>$merchant->id,'amount'=>$data['amount'],'admin_fee'=>$fee,'net_amount'=>$data['amount']-$fee,'reference'=>'WD-'.strtoupper(Str::random(12)),'status'=>'requested']);
        $wallet->debit($merchant, $data['amount'], 'withdrawal-'.$withdrawal->id, 'Penarikan '.$withdrawal->reference, $withdrawal);
        return back()->with('status','Permintaan penarikan dibuat. Dana akan dicairkan setelah diproses Duitku.');
    }
}
