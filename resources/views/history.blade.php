<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Riwayat Pesanan - Save Bite</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, ::before, ::after { box-sizing: border-box; }
        html, body {
            max-width: 100vw;
            overflow-x: hidden !important;
            margin: 0;
            padding: 0;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }

        /* Subtle fade edges on the scrollable tab bar so it reads as scrollable */
        .tab-scroll {
            -webkit-mask-image: linear-gradient(to right, transparent 0, black 16px, black calc(100% - 24px), transparent 100%);
                    mask-image: linear-gradient(to right, transparent 0, black 16px, black calc(100% - 24px), transparent 100%);
        }

        /* Nicer active-press feedback than active:scale-95 jank on some browsers */
        .press-95:active { transform: scale(0.96); }
        .press-98:active { transform: scale(0.98); }

        @keyframes sheet-up {
            from { transform: translateY(24px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .sheet-anim { animation: sheet-up .22s ease-out; }

        /* On tablet/desktop the phone-width container becomes a centered "app card"
           floating on the page background, instead of stretching edge-to-edge. */
        @media (min-width: 640px) {
            body { background: #eef1f6; }
            .app-shell {
                margin-top: 1.75rem;
                margin-bottom: 1.75rem;
                border-radius: 1.75rem;
                box-shadow: 0 1px 2px rgba(15,23,42,.04), 0 24px 48px -12px rgba(15,23,42,.14);
                border: 1px solid rgba(15,23,42,.06);
                min-height: calc(100vh - 3.5rem);
            }
            .app-shell > header { border-top-left-radius: 1.75rem; border-top-right-radius: 1.75rem; }
        }

        /* On real laptop/desktop widths, give the floating card a bit more breathing
           room from the viewport edges so it doesn't feel cramped or overly "boxed". */
        @media (min-width: 1024px) {
            .app-shell {
                margin-top: 2.5rem;
                margin-bottom: 2.5rem;
            }
        }

        /*
         * FIX LAYOUT — Kunci ukuran thumbnail produk secara eksplisit.
         * Sebelumnya thumbnail hanya mengandalkan class Tailwind (w-20 h-20 dll),
         * dan pada beberapa kondisi render (mis. CSS belum ter-compile penuh,
         * atau gambar berukuran sangat besar) elemen ini bisa "menang" melawan
         * containernya dan melebar mengikuti rasio asli gambar, sehingga kartu
         * pesanan jadi berantakan seperti di screenshot.
         * Class ini dipakai sebagai pengaman tambahan di atas Tailwind, murni
         * cosmetic/layout — tidak menyentuh logika Blade/PHP sama sekali.
         */
        .order-thumb {
            width: 5rem;
            height: 5rem;
            min-width: 5rem;
            min-height: 5rem;
            max-width: 5rem;
            max-height: 5rem;
            flex: 0 0 auto;
        }
        @media (min-width: 1024px) {
            .order-thumb {
                width: 6rem;
                height: 6rem;
                min-width: 6rem;
                min-height: 6rem;
                max-width: 6rem;
                max-height: 6rem;
            }
        }
        .order-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }

        .order-thumb-sm {
            width: 3rem;
            height: 3rem;
            min-width: 3rem;
            min-height: 3rem;
            max-width: 3rem;
            max-height: 3rem;
            flex: 0 0 auto;
        }
        @media (min-width: 1024px) {
            .order-thumb-sm {
                width: 3.5rem;
                height: 3.5rem;
                min-width: 3.5rem;
                min-height: 3.5rem;
                max-width: 3.5rem;
                max-height: 3.5rem;
            }
        }
        .order-thumb-sm img { width: 100%; height: 100%; object-fit: cover; display: block; }
    </style>
</head>

@php
    $latestOrderStatus = $orders->first()?->status;
    $latestOrderMessage = match ($latestOrderStatus ?? '') {
        'pending', 'unpaid' => 'Pesanan kamu sedang menunggu pembayaran.',
        'ready_for_pickup' => 'Pesanan kamu sudah siap diambil. Yuk ambil sekarang!',
        'completed', 'served', 'redeemed' => 'Pesanan kamu sudah berhasil diselesaikan. Terima kasih sudah memesan!',
        'cancelled' => 'Pesanan kamu dibatalkan. Silakan cek kembali menu favoritmu.',
        default => 'Status pesanan kamu diperbarui.',
    };

    $getCategory = function ($status) {
        return match ($status) {
            'pending', 'unpaid', 'waiting_payment' => 'pending',
            'paid', 'preparing', 'processing', 'process' => 'preparing',
            'ready_for_pickup', 'ready' => 'ready_for_pickup',
            'completed', 'served', 'done' => 'completed',
            default => 'pending',
        };
    };

    $getPaymentMethodLabel = function ($order) {
        $methodCode = strtoupper(trim((string) ($order->payment_method ?? '')));
        $duitkuLabels = [
            'SP' => 'QRIS / ShopeePay',
            'NQ' => 'QRIS Nobu',
            'GQ' => 'QRIS Gudang Voucher',
            'SQ' => 'QRIS Nusapay',
            'BC' => 'VA Bank BCA',
            'DA' => 'DANA',
            'OV' => 'OVO',
            'SA' => 'ShopeePay App',
            'JP' => 'Jenius Pay',
        ];

        if (isset($duitkuLabels[$methodCode])) return $duitkuLabels[$methodCode];

        $raw = strtolower(json_encode([
            $order->payment_method ?? '',
            $order->payment_type ?? '',
            $order->bank ?? '',
            $order->va_number ?? '',
            $order->biller_code ?? '',
            $order->payment_info ?? '',
            $order->payment_details ?? '',
        ]));

        if (str_contains($raw, 'mandiri') || str_contains($raw, 'echannel') || str_contains($raw, 'bill')) return 'Mandiri Bill Payment';
        if (str_contains($raw, 'bca')) return 'VA Bank BCA';
        if (str_contains($raw, 'bni')) return 'VA Bank BNI';
        if (str_contains($raw, 'bri')) return 'VA Bank BRI';
        if (str_contains($raw, 'permata')) return 'VA Bank Permata';
        if (str_contains($raw, 'cimb')) return 'VA Bank CIMB';
        if (str_contains($raw, 'qris')) return 'QRIS';
        if (str_contains($raw, 'gopay')) return 'GoPay';
        if (str_contains($raw, 'shopeepay')) return 'ShopeePay';
        if (str_contains($raw, 'bank_transfer') || str_contains($raw, 'va')) return 'Virtual Account';

        if (! empty($order->payment_method)) return strtoupper($order->payment_method);
        if (! empty($order->payment_type)) return strtoupper($order->payment_type);

        // Pesanan lama yang belum memiliki data dari Midtrans tidak boleh
        // ditampilkan sebagai VA secara keliru.
        return 'Metode tidak tersedia';
    };

    $pendingCount = $orders->filter(fn ($o) => $getCategory($o->status) === 'pending')->count();
    $preparingCount = $orders->filter(fn ($o) => $getCategory($o->status) === 'preparing')->count();
    $readyCount = $orders->filter(fn ($o) => $getCategory($o->status) === 'ready_for_pickup')->count();
    $completedCount = $orders->filter(fn ($o) => $getCategory($o->status) === 'completed')->count();
    $totalCount = $orders->count();
@endphp

<body class="bg-slate-100 min-h-screen text-slate-800 antialiased"
      x-data="historyState()"
      x-init="init()">

    <!-- Kontainer: lebar HP di mobile, menjadi kartu aplikasi yang melebar bertahap di tablet & desktop -->
    <div class="app-shell w-full max-w-md sm:max-w-2xl lg:max-w-5xl xl:max-w-6xl 2xl:max-w-7xl mx-auto min-h-screen bg-slate-50 pb-24 overflow-x-hidden flex flex-col relative">

        <div x-show="toastVisible" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="mx-4 sm:mx-6 lg:mx-8 mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 shadow-sm px-4 py-3 flex items-center gap-3">
            <span class="text-lg">✨</span>
            <p class="text-xs sm:text-sm font-bold" x-text="toastMessage || 'Status pesanan kamu diperbarui.'">{{ $latestOrderMessage }}</p>
        </div>

        <!-- Top Navigation Header -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-sm border-b border-slate-200/80 shadow-sm w-full">
            <div class="px-4 sm:px-6 lg:px-8 py-3 sm:py-4 lg:py-5 flex items-center justify-between border-b border-slate-100 w-full">
                <div class="flex items-center gap-3 lg:gap-4 min-w-0">
                    <a href="{{ route('home') }}" class="w-9 h-9 sm:w-10 sm:h-10 lg:w-11 lg:h-11 flex items-center justify-center rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 transition press-95 shrink-0">
                        <svg class="w-5 h-5 lg:w-5 lg:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                    </a>
                    <div class="min-w-0">
                        <h1 class="text-base sm:text-lg lg:text-xl font-extrabold text-slate-900 leading-none truncate">Riwayat Pesanan</h1>
                        <p class="text-[11px] sm:text-xs lg:text-sm text-slate-500 font-medium mt-1 truncate">Kelola transaksi kuliner hematmu</p>
                    </div>
                </div>
                <div class="w-9 h-9 sm:w-10 sm:h-10 lg:w-11 lg:h-11 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-base sm:text-lg lg:text-xl font-bold shrink-0 ring-1 ring-emerald-100">
                    🛍️
                </div>
            </div>

            <!-- Tab Filter: bisa digeser di layar sempit, merenggang rapi begitu ada ruang -->
            <div class="tab-scroll w-full overflow-x-auto sm:overflow-x-visible no-scrollbar px-4 sm:px-6 lg:px-8 py-2.5 sm:py-3 lg:py-3.5 bg-white flex sm:flex-wrap items-center gap-2 lg:gap-3 whitespace-nowrap">
                <button @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'bg-slate-900 text-white font-extrabold shadow-sm' : 'bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200'"
                        class="px-4 lg:px-5 py-2 lg:py-2.5 rounded-full text-xs lg:text-sm shrink-0 transition-all duration-150 outline-none press-95">
                    Semua @if($totalCount > 0)<span class="opacity-70">({{ $totalCount }})</span>@endif
                </button>
                <button @click="activeTab = 'pending'"
                        :class="activeTab === 'pending' ? 'bg-slate-900 text-white font-extrabold shadow-sm' : 'bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200'"
                        class="px-4 lg:px-5 py-2 lg:py-2.5 rounded-full text-xs lg:text-sm shrink-0 transition-all duration-150 outline-none press-95">
                    Belum Bayar @if($pendingCount > 0)<span class="opacity-70">({{ $pendingCount }})</span>@endif
                </button>
                <button @click="activeTab = 'preparing'"
                        :class="activeTab === 'preparing' ? 'bg-slate-900 text-white font-extrabold shadow-sm' : 'bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200'"
                        class="px-4 lg:px-5 py-2 lg:py-2.5 rounded-full text-xs lg:text-sm shrink-0 transition-all duration-150 outline-none press-95">
                    Sedang Disiapkan @if($preparingCount > 0)<span class="opacity-70">({{ $preparingCount }})</span>@endif
                </button>
                <button @click="activeTab = 'ready_for_pickup'"
                        :class="activeTab === 'ready_for_pickup' ? 'bg-slate-900 text-white font-extrabold shadow-sm' : 'bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200'"
                        class="px-4 lg:px-5 py-2 lg:py-2.5 rounded-full text-xs lg:text-sm shrink-0 transition-all duration-150 outline-none press-95">
                    Siap Diambil @if($readyCount > 0)<span class="opacity-70">({{ $readyCount }})</span>@endif
                </button>
                <button @click="activeTab = 'completed'"
                        :class="activeTab === 'completed' ? 'bg-slate-900 text-white font-extrabold shadow-sm' : 'bg-slate-100 text-slate-600 font-semibold hover:bg-slate-200'"
                        class="px-4 lg:px-5 py-2 lg:py-2.5 rounded-full text-xs lg:text-sm shrink-0 transition-all duration-150 outline-none press-95">
                    Selesai @if($completedCount > 0)<span class="opacity-70">({{ $completedCount }})</span>@endif
                </button>
            </div>
        </header>

        <!-- Main Content List: kolom tunggal di HP, jadi grid 2-3 kolom di tablet/desktop -->
        <main class="w-full px-4 sm:px-6 lg:px-8 pt-4 sm:pt-6 lg:pt-8 space-y-3 sm:space-y-0 sm:grid sm:grid-cols-2 lg:grid-cols-3 sm:gap-4 lg:gap-5 sm:items-start flex-1">
            @foreach ($orders as $order)
                @php
                    $product = $order->product;
                    $merchant = $product->merchant ?? null;
                    $cat = $getCategory($order->status);

                    $unitPrice = $order->item_price ?? $product->discount_price ?? $product->price ?? 12000;
                    $foodSubtotal = $unitPrice * $order->quantity;
                    $handlingFee = $order->handling_fee ?? $order->service_fee ?? ($order->total_price - $foodSubtotal);
                    if ($handlingFee <= 0) { $handlingFee = 3000; }

                    $rawImg = $product->image ?? $product->photo ?? $product->image_url ?? null;
                    $imgUrl = null;
                    if ($rawImg) {
                        if (\Illuminate\Support\Str::startsWith($rawImg, ['http://', 'https://'])) {
                            $imgUrl = $rawImg;
                        } else {
                            $cleanPath = ltrim(str_replace(['public/', 'storage/'], '', $rawImg), '/');
                            $imgUrl = asset('storage/' . $cleanPath);
                        }
                    }

                    $badgeConfig = match($cat) {
                        'preparing' => ['label' => 'Sedang Disiapkan', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
                        'ready_for_pickup' => ['label' => 'Siap Diambil', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
                        'completed' => ['label' => 'Selesai', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                        default => ['label' => 'Menunggu Bayar', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
                    };

                    $ticketCode = $order->booking_code ?? $order->id;
                    $ticketUrl = \Illuminate\Support\Facades\Route::has('ticket')
                        ? route('ticket', $ticketCode)
                        : url('/ticket/' . $ticketCode);

                    $paymentUrl = \Illuminate\Support\Facades\Route::has('payment.page')
                        ? route('payment.page', $order->id)
                        : url('/payment/' . $order->id);
                    $paymentSyncUrl = route('payment.sync', ['bookingCode' => $order->booking_code]);
                @endphp

                <div x-data="{ openDetail: false }"
                     x-show="activeTab === 'all' || activeTab === '{{ $cat }}'"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden w-full h-fit transition-colors hover:border-slate-300 sm:hover:shadow-md">

                    <!-- Card Header -->
                    <div class="px-3.5 lg:px-4 py-2.5 lg:py-3 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-sm lg:text-base shrink-0">🏪</span>
                            <div class="min-w-0">
                                <h3 class="text-xs lg:text-sm font-bold text-slate-800 truncate">
                                    {{ $merchant->name ?? 'Mitra Save Bite' }}
                                </h3>
                                <p class="text-[10px] lg:text-[11px] text-slate-400 font-medium mt-1 truncate">
                                    {{ $order->created_at ? $order->created_at->format('d M Y • H:i') : '-' }} WIB
                                </p>
                            </div>
                        </div>

                        <span class="px-2.5 lg:px-3 py-1 lg:py-1.5 rounded-full text-[10px] lg:text-[11px] font-bold border {{ $badgeConfig['class'] }} shrink-0 whitespace-nowrap">
                            {{ $badgeConfig['label'] }}
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div @click="openDetail = true" class="flex items-center gap-3 lg:gap-4 p-3 lg:p-4 w-full cursor-pointer hover:bg-slate-50/50 active:bg-slate-50 transition">
                        <div class="order-thumb rounded-xl overflow-hidden bg-slate-100 border border-slate-200 flex items-center justify-center">
                            @if($imgUrl)
                                <img src="{{ $imgUrl }}" alt="{{ $product->title ?? 'Makanan' }}"
                                     loading="lazy"
                                     onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'text-2xl\'>🍔</span>';">
                            @else
                                <span class="text-2xl lg:text-3xl">🍔</span>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs lg:text-sm font-bold text-slate-900 truncate leading-snug">
                                {{ $product->title ?? 'Produk Makanan' }}
                            </h4>
                            <p class="text-[11px] lg:text-xs text-slate-500 mt-1 truncate flex items-center gap-1">
                                <span class="shrink-0">📍</span>
                                <span class="truncate">{{ $product->pickup_location ?? $merchant->address ?? 'Lokasi Mitra' }}</span>
                            </p>
                            <p class="text-[11px] lg:text-xs font-medium text-slate-500 mt-1.5">
                                {{ $order->quantity }}x @ <span class="font-bold text-slate-800">Rp {{ number_format($unitPrice, 0, ',', '.') }}</span>
                            </p>
                        </div>

                        <svg class="w-4 h-4 lg:w-5 lg:h-5 text-slate-300 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>

                    <!-- Card Footer -->
                    <div class="px-3.5 lg:px-4 py-2.5 lg:py-3 bg-slate-50/40 border-t border-slate-100 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="text-[9px] lg:text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Total Pembayaran</span>
                            <span class="text-sm lg:text-base font-extrabold text-emerald-600">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button"
                                    onclick="event.stopPropagation();"
                                    @click="openDetail = true"
                                    class="px-3 lg:px-3.5 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition press-95">
                                Rincian
                            </button>

                            @if($cat === 'pending')
                                <button type="button"
                                        onclick="event.stopPropagation(); syncPendingPayment('{{ $paymentSyncUrl }}', this);"
                                        class="px-3 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-bold bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200 transition press-95">
                                    Cek Status
                                </button>
                                <a href="{{ $paymentUrl }}"
                                   onclick="event.stopPropagation();"
                                   class="inline-flex items-center gap-1 px-3.5 lg:px-4 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition press-95 cursor-pointer shadow-sm shadow-emerald-600/20">
                                    Bayar
                                </a>
                            @elseif($cat === 'completed')
                                <a href="{{ route('food.detail', $order->product_id) }}"
                                   onclick="event.stopPropagation();"
                                   class="inline-flex items-center gap-1 px-3 lg:px-3.5 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition press-95 cursor-pointer">
                                    ★ Ulas
                                </a>
                            @else
                                <a href="{{ $ticketUrl }}"
                                   onclick="event.stopPropagation();"
                                   class="inline-flex items-center gap-1 px-3 lg:px-3.5 py-1.5 lg:py-2 rounded-xl text-xs lg:text-sm font-bold bg-slate-900 hover:bg-slate-800 text-white transition press-95 cursor-pointer">
                                    Tiket
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Modal Detail Pesanan -->
                    <div x-show="openDetail"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         x-cloak
                         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 backdrop-blur-sm p-0 sm:p-4">

                        <div @click.away="openDetail = false"
                             class="sheet-anim bg-white w-full max-w-md sm:max-w-lg lg:max-w-xl rounded-t-3xl sm:rounded-3xl sm:shadow-2xl max-h-[90vh] overflow-y-auto no-scrollbar p-5 sm:p-6 lg:p-7 space-y-4 lg:space-y-5 text-left">

                            <!-- Drag handle (mobile sheet affordance) -->
                            <div class="w-10 h-1.5 bg-slate-200 rounded-full mx-auto -mt-1 sm:hidden"></div>

                            <div class="flex items-center justify-between border-b border-slate-100 pb-3 lg:pb-4">
                                <div>
                                    <h3 class="text-sm lg:text-base font-extrabold text-slate-900">Detail Pesanan</h3>
                                    <p class="text-[10px] lg:text-xs text-slate-400 font-medium mt-0.5">Kode: <span class="font-mono text-slate-600 font-bold">{{ $order->booking_code ?? $order->id }}</span></p>
                                </div>
                                <button @click="openDetail = false" class="w-8 h-8 lg:w-9 lg:h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold hover:bg-slate-200 transition press-95 shrink-0">
                                    ✕
                                </button>
                            </div>

                            <div class="bg-slate-50 p-3 lg:p-4 rounded-2xl border border-slate-100 space-y-2">
                                <div class="text-xs lg:text-sm font-bold text-slate-800 flex items-center gap-1.5">
                                    🏪 {{ $merchant->name ?? 'Mitra Save Bite' }}
                                </div>
                                <div class="flex items-center gap-3 pt-1">
                                    <div class="order-thumb-sm rounded-lg bg-white border border-slate-200 overflow-hidden flex items-center justify-center">
                                        @if($imgUrl)
                                            <img src="{{ $imgUrl }}" alt="{{ $product->title ?? 'Makanan' }}">
                                        @else
                                            🍔
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h5 class="text-xs lg:text-sm font-bold text-slate-900 truncate">{{ $product->title ?? 'Produk Makanan' }}</h5>
                                        <p class="text-[10px] lg:text-xs text-slate-500 mt-0.5 truncate">📍 {{ $product->pickup_location ?? $merchant->address ?? 'Lokasi Mitra' }}</p>
                                        <p class="text-[10px] lg:text-xs font-semibold text-slate-700 mt-1">{{ $order->quantity }}x @ Rp {{ number_format($unitPrice, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <h4 class="text-[11px] lg:text-xs font-bold text-slate-400 uppercase tracking-wider">Riwayat Waktu</h4>
                                <div class="bg-white border border-slate-100 rounded-2xl p-3 lg:p-4 space-y-2.5 text-xs lg:text-sm">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-slate-500 flex items-center gap-1.5 shrink-0">📝 Waktu Dipesan</span>
                                        <span class="font-bold text-slate-800 text-right">
                                            {{ $order->created_at ? $order->created_at->format('d M Y, H:i') : '-' }} WIB
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-slate-500 flex items-center gap-1.5 shrink-0">💳 Waktu Lunas</span>
                                        <span class="font-bold text-emerald-600 text-right">
                                            @if($order->paid_at)
                                                {{ \Carbon\Carbon::parse($order->paid_at)->format('d M Y, H:i') }} WIB
                                            @elseif(in_array($cat, ['preparing', 'ready_for_pickup', 'completed']))
                                                {{ $order->updated_at ? $order->updated_at->format('d M Y, H:i') : '-' }} WIB
                                            @else
                                                <span class="text-amber-500 font-medium">Belum Dibayar</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-slate-500 flex items-center gap-1.5 shrink-0">✅ Waktu Selesai</span>
                                        <span class="font-bold text-slate-800 text-right">
                                            @if($order->completed_at)
                                                {{ \Carbon\Carbon::parse($order->completed_at)->format('d M Y, H:i') }} WIB
                                            @elseif($cat === 'completed')
                                                {{ $order->updated_at ? $order->updated_at->format('d M Y, H:i') : '-' }} WIB
                                            @else
                                                <span class="text-slate-400 font-normal">-</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <h4 class="text-[11px] lg:text-xs font-bold text-slate-400 uppercase tracking-wider">Rincian Pembayaran</h4>
                                <div class="bg-white border border-slate-100 rounded-2xl p-3 lg:p-4 space-y-2 text-xs lg:text-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Metode Pembayaran</span>
                                        <span class="font-bold text-slate-900 uppercase">
                                            {{ $getPaymentMethodLabel($order) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Harga Makanan ({{ $order->quantity }}x)</span>
                                        <span class="font-semibold text-slate-800">Rp {{ number_format($foodSubtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Biaya Penanganan</span>
                                        <span class="font-semibold text-slate-800">Rp {{ number_format($handlingFee, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="border-t border-slate-100 pt-2 flex items-center justify-between">
                                        <span class="font-extrabold text-slate-900">Total Pembayaran</span>
                                        <span class="font-extrabold text-sm lg:text-base text-emerald-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <button @click="openDetail = false" class="w-full py-3 lg:py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs lg:text-sm rounded-xl transition press-98">
                                Tutup Detail
                            </button>

                        </div>
                    </div>

                </div>
            @endforeach

            <!-- Empty States -->
            <div x-show="activeTab === 'pending' && {{ $pendingCount }} === 0" class="sm:col-span-2 lg:col-span-3 text-center py-14 lg:py-16 px-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm mt-3 sm:mt-0 w-full">
                <div class="w-14 h-14 lg:w-16 lg:h-16 bg-slate-100 text-slate-700 rounded-full flex items-center justify-center text-2xl lg:text-3xl mx-auto mb-3">💳</div>
                <h3 class="text-xs lg:text-sm font-bold text-slate-800">Tidak ada pesanan belum dibayar</h3>
                <p class="text-[11px] lg:text-xs text-slate-400 mt-1 max-w-[220px] lg:max-w-xs mx-auto leading-relaxed">Semua tagihan pesananmu telah diselesaikan.</p>
            </div>

            <div x-show="activeTab === 'preparing' && {{ $preparingCount }} === 0" class="sm:col-span-2 lg:col-span-3 text-center py-14 lg:py-16 px-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm mt-3 sm:mt-0 w-full">
                <div class="w-14 h-14 lg:w-16 lg:h-16 bg-slate-100 text-slate-700 rounded-full flex items-center justify-center text-2xl lg:text-3xl mx-auto mb-3">🍳</div>
                <h3 class="text-xs lg:text-sm font-bold text-slate-800">Tidak ada pesanan sedang disiapkan</h3>
                <p class="text-[11px] lg:text-xs text-slate-400 mt-1 max-w-[220px] lg:max-w-xs mx-auto leading-relaxed">Belum ada makanan yang sedang dimasak atau dikemas.</p>
            </div>

            <div x-show="activeTab === 'ready_for_pickup' && {{ $readyCount }} === 0" class="sm:col-span-2 lg:col-span-3 text-center py-14 lg:py-16 px-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm mt-3 sm:mt-0 w-full">
                <div class="w-14 h-14 lg:w-16 lg:h-16 bg-slate-100 text-slate-700 rounded-full flex items-center justify-center text-2xl lg:text-3xl mx-auto mb-3">🛍️</div>
                <h3 class="text-xs lg:text-sm font-bold text-slate-800">Tidak ada pesanan siap diambil</h3>
                <p class="text-[11px] lg:text-xs text-slate-400 mt-1 max-w-[220px] lg:max-w-xs mx-auto leading-relaxed">Belum ada makanan yang siap kamu ambil di toko.</p>
            </div>

            <div x-show="activeTab === 'completed' && {{ $completedCount }} === 0" class="sm:col-span-2 lg:col-span-3 text-center py-14 lg:py-16 px-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm mt-3 sm:mt-0 w-full">
                <div class="w-14 h-14 lg:w-16 lg:h-16 bg-slate-100 text-slate-700 rounded-full flex items-center justify-center text-2xl lg:text-3xl mx-auto mb-3">✅</div>
                <h3 class="text-xs lg:text-sm font-bold text-slate-800">Belum ada pesanan selesai</h3>
                <p class="text-[11px] lg:text-xs text-slate-400 mt-1 max-w-[220px] lg:max-w-xs mx-auto leading-relaxed">Riwayat pesanan yang sudah diambil akan muncul di sini.</p>
            </div>

            <div x-show="activeTab === 'all' && {{ $totalCount }} === 0" class="sm:col-span-2 lg:col-span-3 text-center py-16 lg:py-20 px-4 bg-white rounded-2xl border border-slate-200/80 shadow-sm mt-3 sm:mt-0 w-full">
                <div class="w-16 h-16 lg:w-20 lg:h-20 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center text-2xl lg:text-3xl mx-auto mb-3">🍽️</div>
                <h3 class="text-xs lg:text-sm font-bold text-slate-800">Belum ada riwayat pesanan</h3>
                <p class="text-[11px] lg:text-xs text-slate-400 mt-1 max-w-[220px] lg:max-w-xs mx-auto leading-relaxed">Ayo selamatkan makanan lezat dengan harga terjangkau!</p>
                <a href="{{ route('home') }}" class="inline-block mt-4 px-5 lg:px-6 py-2.5 lg:py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs lg:text-sm font-bold rounded-xl shadow-sm shadow-emerald-600/20 transition press-95">
                    Jelajahi Makanan
                </a>
            </div>
        </main>

    </div>

    <script>
        async function syncPendingPayment(url, button) {
            const originalText = button.textContent;
            button.disabled = true;
            button.textContent = 'Mengecek...';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                });
                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || 'Status belum dapat dicek.');
                }

                if (result.status === 'ready_for_pickup') {
                    window.location.reload();
                    return;
                }

                alert(result.status === 'pending'
                    ? (result.message || 'Pembayaran masih menunggu konfirmasi.')
                    : (result.message || 'Status pembayaran telah diperbarui.'));
            } catch (error) {
                alert(error.message || 'Gagal mengecek status pembayaran. Coba lagi sebentar.');
            } finally {
                button.disabled = false;
                button.textContent = originalText;
            }
        }

        function historyState() {
            return {
                activeTab: 'all',
                latestStatus: @json($latestOrderStatus ?? null),
                toastMessage: @json($latestOrderMessage),
                toastVisible: true,

                init() {
                    setInterval(() => this.refreshStatus(), 10000);
                    setTimeout(() => {
                        this.toastVisible = !!this.latestStatus;
                    }, 200);
                },

                refreshStatus() {
                    fetch('{{ route('customer.orders.status') }}', {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data || !data.status) {
                            return;
                        }

                        if (this.latestStatus !== data.status) {
                            this.latestStatus = data.status;
                            this.toastMessage = data.message;
                            this.toastVisible = true;
                            setTimeout(() => {
                                this.toastVisible = false;
                            }, 5000);
                        }
                    })
                    .catch(() => {});
                }
            };
        }
    </script>

@include('components.live-clock')
</body>
</html>
