<?php

namespace App\Http\Controllers;

use App\Helpers\DistanceCalculator;
use App\Services\DuitkuService;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;
use Midtrans\Transaction;

class FoodController extends Controller
{
    /**
     * Halaman Utama - Daftar Produk Makanan (dengan sorting jarak)
     */
    public function index(Request $request)
    {
        $products = Product::with('merchant')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('stock', '>', 0)
            ->where('is_active', true)
            ->latest()
            ->get();

        // Ambil lokasi GPS dari browser. Nilai terakhir disimpan di sesi agar
        // rekomendasi tetap tersedia saat pengguna kembali ke halaman utama.
        $userLat = $request->query('lat') ?? session('user_latitude');
        $userLon = $request->query('lon') ?? session('user_longitude');
        $hasUserLocation = is_numeric($userLat)
            && is_numeric($userLon)
            && (float) $userLat >= -90
            && (float) $userLat <= 90
            && (float) $userLon >= -180
            && (float) $userLon <= 180;

        if ($hasUserLocation && $request->has(['lat', 'lon'])) {
            $timezone = (float) $userLon >= 129 ? 'Asia/Jayapura' : ((float) $userLon >= 117 ? 'Asia/Makassar' : 'Asia/Jakarta');
            session([
                'user_latitude' => (float) $userLat,
                'user_longitude' => (float) $userLon,
                'user_timezone' => $timezone,
            ]);
            if (Auth::check()) Auth::user()->update(['timezone' => $timezone]);
        }

        // Jika ada user location, hitung jarak dan sort
        if ($hasUserLocation) {
            $userLat = floatval($userLat);
            $userLon = floatval($userLon);

            // Hitung jarak untuk setiap produk
            $products = $products->map(function ($product) use ($userLat, $userLon) {
                if ($product->merchant && $product->merchant->hasLocation()) {
                    $distance = DistanceCalculator::calculate(
                        $userLat,
                        $userLon,
                        $product->merchant->latitude,
                        $product->merchant->longitude
                    );
                    $product->distance_km = $distance;
                    $product->distance_formatted = DistanceCalculator::formatDistance($distance);
                } else {
                    $product->distance_km = null;
                    $product->distance_formatted = null;
                }
                return $product;
            });

            // Sort berdasarkan jarak (yang punya lokasi dulu, dari terdekat ke terjauh)
            $products = $products->sortBy(function ($product) {
                return $product->distance_km ?? PHP_INT_MAX;
            });
        }

        // Setelah login, semua menu tetap ditampilkan. Menu ber-rating tertinggi
        // didahulukan, lalu diurutkan lagi berdasarkan jarak. Menu baru yang
        // belum memiliki ulasan ditempatkan setelah menu yang sudah ber-rating.
        $isFiveStarRecommendation = Auth::check() && $hasUserLocation;
        $recommendationRating = null;
        if ($isFiveStarRecommendation) {
            $ratedProducts = $products->filter(
                fn (Product $product) => $product->reviews_avg_rating !== null
            );

            if ($ratedProducts->isNotEmpty()) {
                $recommendationRating = (float) $ratedProducts->max('reviews_avg_rating');
            }

            $products = $products->sort(function (Product $first, Product $second) {
                $firstRating = $first->reviews_avg_rating === null ? -1 : (float) $first->reviews_avg_rating;
                $secondRating = $second->reviews_avg_rating === null ? -1 : (float) $second->reviews_avg_rating;

                if ($firstRating !== $secondRating) {
                    return $secondRating <=> $firstRating;
                }

                return ($first->distance_km ?? PHP_FLOAT_MAX) <=> ($second->distance_km ?? PHP_FLOAT_MAX);
            })->values();
        }

        return view('index', compact(
            'products',
            'userLat',
            'userLon',
            'hasUserLocation',
            'isFiveStarRecommendation',
            'recommendationRating'
        ));
    }

    /**
     * Halaman Detail Produk
     */
    public function detail(string|int $id)
    {
        $product = Product::with(['merchant', 'reviews.user'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->findOrFail($id);

        $eligibleOrder = null;
        if (Auth::check()) {
            $eligibleOrder = Order::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->whereIn('status', ['completed', 'served'])
                ->doesntHave('review')
                ->first();
        }

        return view('detail', compact('product', 'eligibleOrder'));
    }

    /**
     * Halaman Checkout Baru (Inisialisasi Snap Midtrans)
     */
    public function checkoutPage(Request $request, string|int $id)
    {
        $product = Product::with('merchant')->findOrFail($id);
        $quantity = max((int) $request->get('quantity', 1), 1);

        $subtotal = $product->discount_price * $quantity;
        $adminFee = ($subtotal < 100000) ? 3000 : 5000;
        $totalPrice = $subtotal + $adminFee;

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $bookingCode = 'SB-' . strtoupper(Str::random(8));

        $params = [
            'transaction_details' => [
                'order_id' => $bookingCode,
                'gross_amount' => (int) $totalPrice,
            ],
            'item_details' => [
                [
                    'id' => 'PROD-' . $product->id,
                    'price' => (int) $product->discount_price,
                    'quantity' => $quantity,
                    'name' => mb_strimwidth($product->title, 0, 45, '...'),
                ],
                [
                    'id' => 'ADMIN_FEE',
                    'price' => (int) $adminFee,
                    'quantity' => 1,
                    'name' => 'Biaya Layanan Penyelamatan',
                ]
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            // SUDAH DIPERBARUI: Semua metode pembayaran non-PayLater
            'enabled_payments' => [
                'credit_card',
                'qris',
                'gopay',
                'shopeepay',
                'bca_va',
                'echannel',
                'bri_va',
                'bni_va',
                'permata_va',
                'cimb_va',
                'other_va',
                'bca_klikbca',
                'bca_klikpay',
                'bri_epay',
                'cimb_clicks',
                'danamon_online',
                'uob_ezpay',
                'indomaret',
                'alfamart',
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return view('checkout', compact(
            'product',
            'quantity',
            'subtotal',
            'adminFee',
            'totalPrice',
            'snapToken',
            'bookingCode'
        ));
    }

    /**
     * Halaman Bayar Pesanan Pending dari Riwayat Transaksi
     */
    public function paymentPage(Request $request, string|int $id, DuitkuService $duitku)
    {
        $order = Order::with('product.merchant')
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $hasMidtransConfig = filled(config('midtrans.server_key')) && filled(config('midtrans.client_key'));
        $requestedPaymentMethod = $request->query('payment_method');

        if (($order->payment_gateway ?? '') === 'duitku' || blank($order->snap_token) || !$hasMidtransConfig) {
            if (blank($requestedPaymentMethod) && blank($order->payment_method)) {
                $paymentMethods = $duitku->selectablePaymentMethods((int) $order->total_price);
                return view('payment-method', compact('order', 'paymentMethods'));
            }

            try {
                $invoice = $duitku->createInvoice($order, $requestedPaymentMethod ?: $order->payment_method);
                $order->update([
                    'gateway_reference' => $invoice['reference'] ?? null,
                    'payment_gateway' => 'duitku',
                    'payment_method' => $requestedPaymentMethod ?: $order->payment_method,
                ]);

                $paymentUrl = $invoice['paymentUrl'] ?? $invoice['payment_url'] ?? route('orders.history');

                return redirect()->away($paymentUrl);
            } catch (\Throwable $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        if (!empty($order->snap_token)) {
            $snapToken = $order->snap_token;
        } else {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id'     => $order->booking_code,
                    'gross_amount' => (int) $order->total_price,
                ],
                'customer_details' => [
                    'first_name' => $order->customer_name ?? Auth::user()->name,
                    'email'      => Auth::user()->email,
                    'phone'      => $order->customer_phone,
                ],
                'enabled_payments' => [
                    'credit_card',
                    'qris',
                    'gopay',
                    'shopeepay',
                    'bca_va',
                    'echannel',
                    'bri_va',
                    'bni_va',
                    'permata_va',
                    'cimb_va',
                    'other_va',
                    'bca_klikbca',
                    'bca_klikpay',
                    'bri_epay',
                    'cimb_clicks',
                    'danamon_online',
                    'uob_ezpay',
                    'indomaret',
                    'alfamart',
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            $order->update(['snap_token' => $snapToken]);
        }

        return view('payment', compact('order', 'snapToken'));
    }

    /**
     * Proses Pembuatan Order (Status Awal Pending)
     */
    public function processCheckout(Request $request)
    {
        $validated = $request->validate([
            'product_id'     => ['required', 'integer', 'exists:products,id'],
            'customer_name'  => ['required', 'string', 'max:255'],
            'quantity'       => ['required', 'integer', 'min:1'],
            'booking_code'   => ['required', 'string', 'max:50'],
            'snap_token'     => ['nullable', 'string'],
        ]);

        $customerPhone = Auth::user()->phone;
        if (blank($customerPhone)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lengkapi nomor WhatsApp pada profil Anda sebelum memesan.',
            ], 422);
        }

        try {
            $bookingCode = DB::transaction(function () use ($validated, $request, $customerPhone) {
                $existingOrder = Order::where('booking_code', $validated['booking_code'])->first();
                if ($existingOrder) {
                    return $existingOrder->booking_code;
                }

                $product = Product::lockForUpdate()->findOrFail($validated['product_id']);
                $quantity = (int) $validated['quantity'];

                if ($product->stock < $quantity) {
                    throw new \Exception('Stok makanan tidak cukup untuk jumlah pesanan Anda.');
                }

                $subtotal = $product->discount_price * $quantity;
                $adminFee = ($subtotal < 100000) ? 3000 : 5000;
                $totalPrice = $subtotal + $adminFee;

                Order::create([
                    'user_id'        => Auth::id(),
                    'product_id'     => $product->id,
                    'booking_code'   => $validated['booking_code'],
                    'snap_token'     => $request->snap_token ?? null,
                    'customer_name'  => $validated['customer_name'],
                    'customer_phone' => $customerPhone,
                    'quantity'       => $quantity,
                    'total_price'    => $totalPrice,
                    'status'         => 'pending',
                    'claim_code'     => null,
                ]);

                return $validated['booking_code'];
            });

            return response()->json([
                'status' => 'success',
                'booking_code' => $bookingCode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Webhook Callback Midtrans
     */
    public function midtransCallback(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            $notif = new Notification();

            $transactionStatus = $notif->transaction_status;
            $rawOrderId        = $notif->order_id;
            $fraudStatus       = $notif->fraud_status;

            $bookingCode = implode('-', array_slice(explode('-', $rawOrderId), 0, 2));

            $order = Order::where('booking_code', $rawOrderId)
                ->orWhere('booking_code', $bookingCode)
                ->first();

            if (!$order) {
                return response()->json(['message' => 'Order tidak ditemukan'], 404);
            }

            $this->applyPaymentStatus(
                $order,
                $transactionStatus,
                $fraudStatus,
                $notif->payment_type ?? null
            );

            return response()->json(['status' => 'success', 'message' => 'Notification processed']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Sinkronkan pembayaran setelah Snap sukses. Status selalu diambil ulang
     * dari API Midtrans menggunakan Server Key, bukan dari browser pelanggan.
     */
    public function syncPaymentStatus(string $bookingCode, DuitkuService $duitku)
    {
        $order = Order::where('booking_code', $bookingCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (($order->payment_gateway ?? '') === 'duitku') {
            try {
                $transaction = $duitku->getTransactionStatus($order);
                $resultCode = (string) ($transaction['statusCode'] ?? '');

                $this->applyPaymentStatus(
                    $order,
                    $resultCode === '00' ? 'settlement' : 'pending',
                    null,
                    $transaction['paymentCode'] ?? null
                );

                return response()->json([
                    'status' => $order->fresh()->status,
                    'message' => $resultCode === '00'
                        ? 'Pembayaran Duitku berhasil dikonfirmasi.'
                        : 'Duitku masih mencatat transaksi ini sebagai menunggu pembayaran.',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Status pembayaran Duitku belum dapat disinkronkan: ' . $e->getMessage(),
                ], 422);
            }
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            $transaction = Transaction::status($order->booking_code);
            $this->applyPaymentStatus(
                $order,
                $transaction->transaction_status,
                $transaction->fraud_status ?? null,
                $transaction->payment_type ?? null
            );

            return response()->json([
                'status' => $order->fresh()->status,
                'message' => 'Status pembayaran berhasil disinkronkan.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Status pembayaran belum dapat disinkronkan: ' . $e->getMessage(),
            ], 422);
        }
    }

    private function applyPaymentStatus(
        Order $order,
        string $transactionStatus,
        ?string $fraudStatus = null,
        ?string $paymentMethod = null
    ): void
    {
        // payment_type berasal langsung dari Midtrans (mis. qris, bank_transfer,
        // gopay). Simpan di server agar riwayat tidak menebak metode pembayaran.
        if (filled($paymentMethod) && $order->payment_method !== $paymentMethod) {
            $order->update(['payment_method' => $paymentMethod]);
        }

        if ($transactionStatus === 'settlement' || ($transactionStatus === 'capture' && $fraudStatus === 'accept')) {
            if (in_array($order->status, ['pending', 'unpaid'])) {
                $order->product()->decrement('stock', $order->quantity);
                $order->update([
                    'status' => 'ready_for_pickup',
                    'claim_code' => 'SB-' . strtoupper(Str::random(6)),
                ]);
            }

            return;
        }

        if ($transactionStatus === 'pending') {
            $order->update(['status' => 'pending']);
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $order->update(['status' => 'cancelled']);
        }
    }

    /**
     * Halaman Tiket/Detail Pesanan Individual Customer
     */
    public function ticket(string $code)
    {
        $order = Order::with('product.merchant')->where('booking_code', $code)->firstOrFail();

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke tiket ini.');
        }

        if (in_array($order->status, ['pending', 'unpaid', 'cancelled'])) {
            return redirect()->route('orders.history')->with('error', 'Pesanan belum dibayar.');
        }

        return view('ticket', compact('order'));
    }

    /**
     * Halaman Riwayat Pesanan Pembeli
     */
    public function history()
    {
        $orders = Order::with(['product.merchant'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('history', compact('orders'));
    }

    public function customerOrderStatus()
    {
        $order = Order::where('user_id', Auth::id())
            ->latest()
            ->first();

        if (!$order) {
            return response()->json([
                'status' => null,
                'message' => 'Belum ada pesanan untukmu saat ini.',
            ]);
        }

        return response()->json([
            'status' => $order->status,
            'message' => $this->getCustomerStatusMessage($order->status),
            'booking_code' => $order->booking_code,
            'updated_at' => $order->updated_at?->toDateTimeString(),
        ]);
    }

    protected function getCustomerStatusMessage(string $status): string
    {
        return match ($status) {
            'pending', 'unpaid' => 'Pesanan kamu sedang menunggu pembayaran.',
            'ready_for_pickup' => 'Pesanan kamu sudah siap diambil. Yuk ambil sekarang!',
            'completed', 'served', 'redeemed' => 'Pesanan kamu sudah berhasil diselesaikan. Terima kasih sudah memesan!',
            'cancelled' => 'Pesanan kamu dibatalkan. Silakan cek kembali menu favoritmu.',
            default => 'Status pesanan kamu diperbarui.',
        };
    }

    /**
     * Dashboard Riwayat Pesanan Customer (Tab Version)
     */
    public function customerOrders()
    {
        $userId = Auth::id();

        $unpaidOrders    = Order::with('product.merchant')->where('user_id', $userId)->whereIn('status', ['pending', 'unpaid'])->latest()->get();
        $readyOrders     = Order::with('product.merchant')->where('user_id', $userId)->where('status', 'ready_for_pickup')->latest()->get();
        $completedOrders = Order::with('product.merchant')->where('user_id', $userId)->whereIn('status', ['completed', 'served'])->latest()->get();

        return view('history', compact('unpaidOrders', 'readyOrders', 'completedOrders'));
    }

    /**
     * Dashboard Merchant - Daftar Makanan Siap Diambil Customer
     */
    public function merchantOrders()
    {
        $merchantId = Auth::user()->merchant->id ?? null;

        $orders = Order::with(['product.merchant', 'user'])
            ->whereHas('product', function ($query) use ($merchantId) {
                $query->where('merchant_id', $merchantId);
            })
            ->where('status', 'ready_for_pickup')
            ->latest()
            ->get();

        return view('merchant.orders', compact('orders'));
    }

    /**
     * Merchant Verifikasi Kode Klaim & Selesaikan Pesanan
     */
    public function completeOrder(Request $request)
    {
        $request->validate([
            'claim_code' => ['required', 'string']
        ]);

        $order = Order::where('claim_code', $request->claim_code)->first();

        if (!$order) {
            return back()->with('error', 'Kode Klaim tidak ditemukan!');
        }

        if ($order->status !== 'ready_for_pickup') {
            return back()->with('error', 'Pesanan tidak dalam status siap diambil!');
        }

        $order->update([
            'status' => 'completed'
        ]);

        return back()->with('success', 'Pesanan berhasil diserahkan ke pelanggan!');
    }

    /**
     * Merchant Update Stok Makanan
     */
    public function updateStock(Request $request, string|int $id)
    {
        $request->validate([
            'stock' => ['required', 'integer', 'min:0']
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'stock' => $request->stock
        ]);

        return back()->with('success', 'Stok makanan berhasil diperbarui!');
    }
}   
