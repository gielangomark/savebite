<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merchant Dashboard - Save Bite</title>
    
    <!-- Fonts & Tailwind CSS -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-emerald-50/30 to-teal-50/40 text-slate-800 min-h-screen antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Top Accent Bar -->
    <div class="h-1.5 bg-gradient-to-r from-emerald-500 via-teal-500 to-amber-500 w-full"></div>

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-emerald-100/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white text-lg shadow-md shadow-emerald-600/30 font-black group-hover:scale-105 transition-transform duration-200">
                    🌱
                </div>
                <div>
                    <span class="text-lg font-black bg-gradient-to-r from-emerald-800 via-teal-700 to-emerald-900 bg-clip-text text-transparent tracking-tight block leading-none">Save Bite</span>
                    <span class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-emerald-600">Merchant Center</span>
                </div>
            </a>

            <!-- Profile & Actions -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-emerald-50/80 border border-emerald-200/80 shadow-inner">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-600 text-white flex items-center justify-center text-xs font-black uppercase shadow-sm">
                        {{ substr(auth()->user()->name ?? 'M', 0, 2) }}
                    </div>
                    <span class="text-xs font-bold text-emerald-950 max-w-[140px] truncate">{{ auth()->user()->name ?? 'Merchant' }}</span>
                </div>

                <a href="{{ route('home') }}" class="rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 px-3.5 py-2 text-xs font-extrabold text-slate-700 transition duration-200 active:scale-95 shadow-sm">
                    🏠 Utamanya
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-xl border border-rose-200 hover:border-rose-300 bg-rose-50/80 hover:bg-rose-50 px-4 py-2 text-xs font-extrabold text-rose-600 transition duration-200 active:scale-95 shadow-sm hover:shadow">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header Welcome Banner -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-xs font-black uppercase tracking-wider mb-2.5 shadow-md shadow-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span> Panel Penjual Live
                </span>
                <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Dashboard Penjual</h1>
                <p class="text-sm font-semibold text-slate-500 mt-1">Kelola menu makanan hemat dan validasi pesanan masuk secara langsung.</p>
            </div>

            <!-- Summary Stats Cards -->
            <div class="flex items-center gap-4">
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 border border-emerald-400/50 rounded-2xl p-4 shadow-lg shadow-emerald-600/20 min-w-[140px] text-white transform hover:-translate-y-1 transition duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase tracking-wider text-emerald-100">Total Menu</p>
                        <span class="text-base">🍲</span>
                    </div>
                    <p class="text-2xl font-black mt-1">{{ $products->count() }} <span class="text-xs text-emerald-100 font-semibold">item</span></p>
                </div>

                <div class="bg-gradient-to-br from-amber-500 to-orange-500 border border-amber-400/50 rounded-2xl p-4 shadow-lg shadow-amber-500/20 min-w-[140px] text-white transform hover:-translate-y-1 transition duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase tracking-wider text-amber-100">Order Masuk</p>
                        <span class="text-base">🛍️</span>
                    </div>
                    <p class="text-2xl font-black mt-1">{{ $orders->count() }} <span class="text-xs text-amber-100 font-semibold">pesanan</span></p>
                </div>
            </div>
        </div>

        <section class="mb-8 grid gap-4 md:grid-cols-2">
            <div class="rounded-2xl bg-slate-900 p-5 text-white"><p class="text-xs font-bold text-slate-300">SALDO TERSEDIA</p><p class="mt-1 text-3xl font-black">Rp {{ number_format($wallet->balance) }}</p><p class="mt-2 text-sm text-slate-300">Pendapatan hari ini: Rp {{ number_format($todayIncome) }}</p></div>
            <form method="POST" action="{{ route('merchant.withdrawals.store') }}" class="rounded-2xl border bg-white p-5">@csrf<p class="font-black">Tarik saldo</p><p class="mb-3 text-xs text-slate-500">Minimal Rp50.000 · biaya admin Rp5.000</p><div class="grid grid-cols-2 gap-2"><input required name="amount" type="number" min="50000" placeholder="Nominal" class="rounded-lg border p-2 text-sm"><input required name="bank_code" placeholder="Kode bank" class="rounded-lg border p-2 text-sm"><input required name="bank_account_number" placeholder="Nomor rekening" class="rounded-lg border p-2 text-sm"><input required name="bank_account_name" placeholder="Nama pemilik" class="rounded-lg border p-2 text-sm"></div><button class="mt-3 rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">Ajukan penarikan</button></form>
        </section>

        <!-- Dynamic Flash Alerts Section -->
        <div id="alert-container">
            @if (session('success') || session('status'))
                <div class="alert-dismissible mb-6 rounded-2xl border border-emerald-400 bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 p-4 text-sm font-bold text-white flex items-center justify-between shadow-lg shadow-emerald-500/20 transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <span class="text-lg">✅</span>
                        <span>{{ session('success') ?? session('status') }}</span>
                    </div>
                    <button type="button" onclick="dismissAlert(this)" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert-dismissible mb-6 rounded-2xl border border-rose-400 bg-gradient-to-r from-rose-500 via-red-500 to-rose-600 p-4 text-sm font-bold text-white flex items-center justify-between shadow-lg shadow-rose-500/20 transition-all duration-300">
                    <div class="flex items-center gap-3">
                        <span class="text-lg">⚠️</span>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="dismissAlert(this)" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-dismissible mb-6 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm font-bold text-amber-800 space-y-1.5 shadow-md transition-all duration-300">
                    <div class="flex items-center justify-between pb-1 border-b border-amber-200/60">
                        <span class="flex items-center gap-2 font-black text-amber-900 text-xs uppercase tracking-wider">
                            <span>⚠️</span> Terjadi Kesalahan Input:
                        </span>
                        <button type="button" onclick="dismissAlert(this)" class="text-amber-500 hover:text-amber-800 text-lg leading-none">&times;</button>
                    </div>
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-2 text-xs font-semibold text-amber-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Main Content Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- LEFT COLUMN (Tambah Menu & Katalog) -->
            <div class="lg:col-span-7 space-y-8">
                
                <!-- SECTION 1: Form Tambah Menu -->
                <section class="rounded-[28px] bg-white border border-slate-200/90 p-6 sm:p-7 shadow-xl shadow-slate-200/50 hover:shadow-2xl transition duration-300">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-lg">
                                ➕
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-900 tracking-tight">Tambah Menu Baru</h2>
                                <p class="text-xs text-slate-400 font-medium">Daftarkan porsi makanan hemat hari ini.</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-black uppercase tracking-wider">
                            Form Input
                        </span>
                    </div>

                    <form method="POST" action="{{ route('merchant.products.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        <!-- Judul Menu -->
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 mb-1.5">Judul Menu</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-3 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 transition outline-none" placeholder="Contoh: Nasi Ayam Geprek Extra Sambal" required>
                        </div>

                        <!-- Jam Ambil, Lokasi & Stok Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 mb-1.5">Waktu Pengambilan</label>
                                <div class="grid grid-cols-2 gap-2"><input type="time" name="pickup_start_time" value="{{ old('pickup_start_time') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-3 text-sm font-semibold" required><input type="time" name="pickup_end_time" value="{{ old('pickup_end_time') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3 py-3 text-sm font-semibold" required></div><p class="mt-1 text-[10px] text-slate-500">Zona waktu toko: {{ $merchant->timezone }}</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 mb-1.5">Tempat Pengambilan</label>
                                <input type="text" name="pickup_location" value="{{ old('pickup_location') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-3 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 transition outline-none" placeholder="Depan Kantin Utama" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 mb-1.5">Stok Porsi</label>
                                <input type="number" name="stock" value="{{ old('stock') }}" min="1" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-3 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 transition outline-none" placeholder="10" required>
                            </div>
                        </div>

                        <!-- Harga Asli, Diskon & Kalkulator Hemat Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Harga Asli (Rp)</label>
                                <input type="number" id="original_price" name="original_price" value="{{ old('original_price') }}" min="0" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-3 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 transition outline-none" placeholder="20000" required>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-700">Harga Diskon (Rp)</label>
                                    <span id="discount-badge" class="hidden text-[10px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">Hemat 0%</span>
                                </div>
                                <input type="number" id="discount_price" name="discount_price" value="{{ old('discount_price') }}" min="0" class="w-full rounded-xl border border-emerald-300 bg-emerald-50/30 px-4 py-3 text-sm font-bold text-emerald-900 placeholder:text-emerald-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 transition outline-none" placeholder="8000" required>
                            </div>
                        </div>

                        <!-- Upload Foto Makanan + Image Preview -->
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Upload Foto Makanan</label>
                            <div class="relative">
                                <input type="file" name="image" accept="image/*" class="hidden" id="product-image-input">
                                <label for="product-image-input" class="w-full rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50/70 p-4 text-sm font-semibold text-slate-600 cursor-pointer hover:border-emerald-400 hover:bg-emerald-50/30 transition flex flex-col items-center justify-center gap-2 text-center">
                                    
                                    <!-- Image Preview Container -->
                                    <div id="image-preview-wrapper" class="hidden relative w-32 h-32 rounded-xl overflow-hidden shadow-md border border-slate-200 mb-1">
                                        <img id="image-preview" src="#" alt="Preview Foto" class="w-full h-full object-cover">
                                    </div>

                                    <div id="upload-placeholder" class="flex flex-col items-center gap-1">
                                        <span class="text-2xl">📸</span>
                                        <span id="file-name" class="font-bold text-slate-700">Klik atau geser foto ke sini</span>
                                        <span class="text-[11px] text-slate-400 font-normal">Format: JPG, PNG, WEBP (Maks. 2MB)</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full mt-3 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-800 px-4 py-3.5 text-sm font-black text-white shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/50 transition duration-200 active:scale-[0.98] flex items-center justify-center gap-2">
                            <span>🚀 Simpan Menu Baru</span>
                        </button>
                    </form>
                </section>

                <!-- SECTION 2: Katalog Menu Saya -->
                <section class="rounded-[28px] bg-white border border-slate-200/90 p-6 sm:p-7 shadow-xl shadow-slate-200/50">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                        <div class="flex items-center gap-2.5">
                            <span class="text-xl">🍱</span>
                            <h2 class="text-lg font-black text-slate-900 tracking-tight">Katalog Menu Saya</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="text" id="search-catalog" placeholder="Cari menu..." class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-emerald-500 outline-none transition w-full sm:w-40">
                            <span class="px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black shrink-0">
                                {{ $products->count() }} Terdaftar
                            </span>
                        </div>
                    </div>

                    <div id="catalog-list" class="space-y-3">
                        @forelse ($products as $product)
                            <div class="catalog-item group flex flex-col sm:flex-row items-start sm:items-center gap-4 rounded-2xl border border-slate-200/80 bg-slate-50/60 p-4 hover:bg-emerald-50/40 hover:border-emerald-400 transition duration-200 hover:shadow-md">
                                <!-- Image Thumbnail -->
                                <div class="relative w-full sm:w-24 h-32 sm:h-24 rounded-xl overflow-hidden bg-slate-200 shrink-0 shadow-sm">
                                    <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=300' }}" 
                                         alt="{{ $product->title }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-slate-900/80 backdrop-blur-sm text-white text-[10px] font-extrabold">
                                        ID: #{{ $product->id }}
                                    </span>
                                </div>

                                <!-- Info Content -->
                                <div class="flex-1 min-w-0 w-full">
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="product-title font-black text-slate-900 truncate text-base group-hover:text-emerald-900 transition">{{ $product->title }}</h3>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-500 text-white text-xs font-black shadow-sm shrink-0">
                                            <span>📦</span> {{ $product->stock }} stok
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2.5 mt-2">
                                        <span class="text-lg font-black text-emerald-700">Rp {{ number_format($product->discount_price) }}</span>
                                        @if($product->original_price)
                                            <span class="text-xs text-slate-400 line-through font-semibold">Rp {{ number_format($product->original_price) }}</span>
                                        @endif
                                    </div>

                                    <!-- Jam Ambil & Tombol Aksi -->
                                    <div class="flex flex-wrap items-center justify-between gap-2 mt-3 pt-2 border-t border-slate-200/60">
                                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-500 bg-white/80 px-2.5 py-1 rounded-lg border border-slate-200/60">
                                            <span class="text-amber-500">⏰</span>
                                            <span>Ambil: {{ $product->pickup_time }}</span>
                                        </div>

                                        <!-- Tombol Action Edit & Hapus -->
                                        <div class="flex items-center gap-2">
                                            <form method="POST" action="{{ route('merchant.products.availability', $product) }}">@csrf @method('PATCH')<button class="rounded-xl px-3 py-1.5 text-xs font-extrabold {{ $product->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</button></form>
                                            <a href="{{ route('merchant.products.edit', $product->id) }}" class="rounded-xl border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-800 px-3 py-1.5 text-xs font-extrabold transition active:scale-95 shadow-sm flex items-center gap-1">
                                                ✏️ Edit
                                            </a>
                                            <form method="POST" action="{{ route('merchant.products.destroy', $product->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 px-3 py-1.5 text-xs font-extrabold transition active:scale-95 shadow-sm flex items-center gap-1">
                                                    🗑️ Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border-2 border-dashed border-emerald-200 bg-gradient-to-b from-slate-50 to-emerald-50/30 px-4 py-12 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                                    🍲
                                </div>
                                <p class="text-base font-black text-slate-800">Belum ada menu yang ditambahkan</p>
                                <p class="text-xs font-semibold text-slate-400 mt-1 max-w-xs mx-auto">Gunakan formulir di atas untuk mendaftarkan porsi makanan pertama Anda.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

            </div>

            <!-- RIGHT COLUMN (Lokasi Toko & Order Masuk & Redeem Form) -->
            <div class="lg:col-span-5 space-y-8">

                <!-- SECTION: LOKASI TOKO (GPS) -->
                <section class="rounded-[28px] bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200/80 p-6 sm:p-7 shadow-lg shadow-emerald-200/30 hover:shadow-xl transition duration-300">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div class="flex items-center gap-3 flex-1">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600 text-white flex items-center justify-center text-lg shadow-md">
                                📍
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-emerald-900 tracking-tight">Lokasi Toko</h2>
                                <p class="text-xs text-emerald-700 font-medium">Atur koordinat agar pembeli bisa melihat jarak terdekat</p>
                            </div>
                        </div>
                        <a href="{{ route('merchant.location.edit') }}" class="rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-3.5 py-2 text-xs font-black shadow-md transition active:scale-95 whitespace-nowrap flex items-center gap-1.5">
                            <span>⚙️</span>
                            <span>Edit Lokasi</span>
                        </a>
                    </div>

                    @if($merchant->hasLocation())
                        <div class="space-y-3">
                            <div class="rounded-xl bg-white/60 border border-emerald-200/60 p-3.5 backdrop-blur-sm">
                                <p class="text-[10px] font-bold text-emerald-900 uppercase tracking-wider mb-1.5">Status</p>
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span class="text-sm font-black text-emerald-800">✓ Lokasi Aktif</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl bg-white/60 border border-emerald-200/60 p-3 backdrop-blur-sm">
                                    <p class="text-[10px] font-bold text-emerald-900 uppercase tracking-wider mb-1">Latitude</p>
                                    <p class="text-xs font-mono font-bold text-emerald-800">{{ $merchant->latitude }}</p>
                                </div>
                                <div class="rounded-xl bg-white/60 border border-emerald-200/60 p-3 backdrop-blur-sm">
                                    <p class="text-[10px] font-bold text-emerald-900 uppercase tracking-wider mb-1">Longitude</p>
                                    <p class="text-xs font-mono font-bold text-emerald-800">{{ $merchant->longitude }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="rounded-xl bg-white/70 border border-amber-300/60 p-4 text-center space-y-2">
                            <p class="text-sm font-black text-amber-900">⚠️ Lokasi Belum Diatur</p>
                            <p class="text-xs text-amber-800 font-semibold">Pembeli tidak bisa melihat jarak produk Anda. Atur lokasi sekarang untuk meningkatkan penjualan.</p>
                            <a href="{{ route('merchant.location.edit') }}" class="inline-block mt-2 rounded-lg bg-amber-600 hover:bg-amber-700 text-white px-4 py-1.5 text-xs font-black transition active:scale-95">
                                ➜ Atur Lokasi Sekarang
                            </a>
                        </div>
                    @endif
                </section>

                <section class="rounded-[28px] bg-white border border-slate-200/90 p-6 sm:p-7 shadow-xl shadow-slate-200/50 sticky top-24">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="text-xl">⚡</span>
                            <div>
                                <h2 class="text-lg font-black text-slate-900 tracking-tight">Pesanan Masuk</h2>
                                <p class="text-xs text-slate-400 font-medium">Validasi penukaran voucher pembeli.</p>
                            </div>
                        </div>
                        <input type="text" id="search-orders" placeholder="Cari nama / kode..." class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-amber-500 outline-none transition w-full sm:w-36">
                    </div>

                    <div id="orders-list" class="space-y-4">
                        @forelse ($orders as $order)
                            @php
                                $isCompleted = in_array(strtolower($order->status ?? ''), ['completed', 'served', 'redeemed', 'selesai', 'success']);
                            @endphp

                            <div class="order-item rounded-2xl border border-slate-200 bg-slate-50/70 p-4 space-y-3 relative overflow-hidden transition hover:border-emerald-400 hover:shadow-md">
                                
                                <!-- Card Header: Product & Status -->
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-md inline-block">
                                            {{ $order->product->title ?? 'Menu Dihapus' }}
                                        </span>
                                        <h3 class="order-customer font-black text-slate-900 text-base mt-1.5">{{ $order->customer_name }}</h3>
                                    </div>
                                    
                                    <!-- Dynamic Status Badge -->
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shrink-0 shadow-sm {{ $isCompleted ? 'bg-emerald-500 text-white' : 'bg-gradient-to-r from-amber-500 to-orange-500 text-white animate-pulse' }}">
                                        {{ $isCompleted ? '✓ Selesai' : '⏳ Menunggu' }}
                                    </span>
                                </div>

                                <!-- Details List -->
                                <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 space-y-2 text-xs shadow-inner">
                                    <div class="flex items-center justify-between text-slate-600">
                                        <span>No. WhatsApp:</span>
                                        <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded">📱 {{ $order->customer_phone }}</span>
                                    </div>
                                    <div class="flex items-center justify-between text-slate-600">
                                        <span>Jumlah Porsi:</span>
                                        <span class="font-bold text-slate-900">{{ $order->quantity }} porsi</span>
                                    </div>
                                    <div class="flex items-center justify-between text-slate-600">
                                        <span>Total Tagihan:</span>
                                        <span class="font-black text-emerald-700 text-sm">Rp {{ number_format($order->total_price) }}</span>
                                    </div>
                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Kode Booking:</span>
                                        <span class="order-code font-black text-sm tracking-widest text-emerald-900 bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-300">
                                            {{ $order->booking_code }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Form Redeem / Label Berhasil -->
                                @if (!$isCompleted)
                                    <form method="POST" action="{{ route('merchant.orders.redeem', $order->id) }}" class="pt-1 redeem-form">
                                        @csrf
                                        <div class="flex items-center gap-2">
                                            <input type="text" 
                                                   name="code" 
                                                   placeholder="Masukkan kode unik..." 
                                                   class="flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-bold text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" 
                                                   required>
                                            <button type="button" 
                                                    class="scan-code-btn rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 px-3 py-2.5 text-xs font-black transition active:scale-95 shadow-sm shrink-0">
                                                📷 Scan
                                            </button>
                                            <button type="submit" 
                                                    class="rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 px-4 py-2.5 text-xs font-black text-white transition active:scale-95 shadow-md shadow-emerald-600/20 shrink-0">
                                                Redeem
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="pt-2 text-center text-xs font-bold text-emerald-700 bg-emerald-50 rounded-xl py-2.5 border border-emerald-200/80 flex items-center justify-center gap-1.5 shadow-sm">
                                        <span>✅</span> Pesanan Telah Berhasil Ditukarkan
                                    </div>
                                @endif

                            </div>
                        @empty
                            <div class="rounded-2xl border-2 border-dashed border-amber-200 bg-gradient-to-b from-slate-50 to-amber-50/30 px-4 py-12 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                                    🛒
                                </div>
                                <p class="text-base font-black text-slate-800">Belum ada pesanan masuk</p>
                                <p class="text-xs font-semibold text-slate-400 mt-1 max-w-xs mx-auto">Pesanan dari pembeli akan otomatis muncul di sini secara real-time.</p>
                            </div>
                        @endforelse
                    </div>
                </section>

            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scanButtons = document.querySelectorAll('.scan-code-btn');
            const scanStatus = document.createElement('div');
            scanStatus.id = 'scan-status';
            scanStatus.className = 'hidden mt-2 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2';
            document.body.appendChild(scanStatus);

            let currentStream = null;
            let scannerActive = false;
            let scannerSession = 0;

            function stopCamera() {
                scannerSession++;
                if (currentStream) {
                    currentStream.getTracks().forEach(track => track.stop());
                    currentStream = null;
                }
                scannerActive = false;
                const video = document.getElementById('scan-video');
                const panel = document.getElementById('scan-camera-panel');
                if (video) video.srcObject = null;
                if (panel) panel.classList.add('hidden');
            }

            function showScanStatus(message, type = 'success') {
                if (!scanStatus) return;

                scanStatus.classList.remove(
                    'hidden',
                    'text-emerald-700',
                    'bg-emerald-50',
                    'border-emerald-200',
                    'text-red-700',
                    'bg-red-50',
                    'border-red-200',
                    'text-amber-700',
                    'bg-amber-50',
                    'border-amber-200'
                );

                const classes = {
                    success: ['text-emerald-700', 'bg-emerald-50', 'border-emerald-200'],
                    warning: ['text-amber-700', 'bg-amber-50', 'border-amber-200'],
                    error: ['text-red-700', 'bg-red-50', 'border-red-200']
                };

                classes[type] = classes[type] || classes.success;
                classes[type].forEach(className => scanStatus.classList.add(className));

                scanStatus.textContent = message;
                scanStatus.classList.remove('hidden');
            }

            function openCameraScanner(button) {
                const isSecureContext = window.isSecureContext || location.hostname === 'localhost' || /^127\./.test(location.hostname);

                if (!isSecureContext) {
                    showScanStatus('Kamera hanya bisa dipakai di halaman HTTPS/localhost. Buka lewat localhost atau https.', 'error');
                    return;
                }

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    showScanStatus('Browser ini tidak mendukung kamera. Gunakan Chrome/Edge di laptop atau HP.', 'error');
                    return;
                }

                if (currentStream) {
                    currentStream.getTracks().forEach(track => track.stop());
                    currentStream = null;
                }
                scannerActive = false;
                const session = ++scannerSession;

                const panel = document.getElementById('scan-camera-panel');
                if (!panel) {
                    const wrapper = document.createElement('div');
                    wrapper.id = 'scan-camera-panel';
                    wrapper.className = 'hidden mt-3 rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-white p-3 shadow-inner';
                    wrapper.innerHTML = `
                        <div class="flex items-center justify-between mb-2 gap-3">
                            <div class="text-[11px] font-black uppercase tracking-[0.18em] text-emerald-700">Scan QR Code</div>
                            <button type="button" id="close-scan-btn" class="text-[10px] font-bold px-2 py-1 rounded-lg border border-slate-200 bg-white text-slate-600">Tutup</button>
                        </div>
                        <video id="scan-video" autoplay playsinline muted class="w-full rounded-xl border border-slate-200 bg-slate-900"></video>
                        <canvas id="scan-canvas" class="hidden"></canvas>
                    `;
                    button.closest('form').parentElement.appendChild(wrapper);
                    document.getElementById('close-scan-btn').addEventListener('click', stopCamera);
                }

                const panelNow = document.getElementById('scan-camera-panel');
                const video = document.getElementById('scan-video');
                const canvas = document.getElementById('scan-canvas');
                const context = canvas.getContext('2d');

                if (!panelNow || !video || !canvas || !context) {
                    showScanStatus('Kamera tidak tersedia saat ini.', 'error');
                    return;
                }

                panelNow.classList.remove('hidden');
                showScanStatus('Arahkan kamera ke QR code tiket pembeli...', 'warning');

                const cameraConstraints = {
                    video: {
                        facingMode: 'environment',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                    audio: false
                };

                navigator.mediaDevices.getUserMedia(cameraConstraints).catch(() => {
                    return navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user',
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        },
                        audio: false
                    });
                }).then(stream => {
                    if (session !== scannerSession) {
                        stream.getTracks().forEach(track => track.stop());
                        return;
                    }

                    currentStream = stream;
                    video.srcObject = stream;
                    video.play().catch(() => {});
                    scannerActive = true;

                    function scanFrame() {
                        if (session !== scannerSession || !scannerActive) {
                            return;
                        }

                        if (!video.videoWidth) {
                            requestAnimationFrame(scanFrame);
                            return;
                        }

                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        context.drawImage(video, 0, 0, canvas.width, canvas.height);

                        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, canvas.width, canvas.height, {
                            inversionAttempts: 'dontInvert'
                        });

                        if (code) {
                            const scannedValue = code.data.trim();
                            const orderCard = [...document.querySelectorAll('.order-item')].find(item => {
                                const codeEl = item.querySelector('.order-code');
                                return codeEl && (codeEl.textContent.trim().toUpperCase() === scannedValue.toUpperCase());
                            });

                            const targetForm = orderCard ? orderCard.querySelector('.redeem-form') : button.closest('form');
                            const targetInput = targetForm ? targetForm.querySelector('input[name="code"]') : null;

                            if (targetInput) {
                                targetInput.value = scannedValue;
                                showScanStatus('Kode berhasil terbaca. Memproses redeem...', 'success');
                                scannerActive = false;
                                setTimeout(() => {
                                    targetForm.requestSubmit();
                                }, 600);
                                stopCamera();
                                return;
                            }
                        }

                        requestAnimationFrame(scanFrame);
                    }

                    requestAnimationFrame(scanFrame);
                }).catch(() => {
                    if (session !== scannerSession) {
                        return;
                    }

                    showScanStatus('Akses kamera ditolak. Izinkan kamera lalu klik tombol Scan lagi.', 'error');
                    stopCamera();
                });
            }

            scanButtons.forEach(button => {
                button.addEventListener('click', function () {
                    openCameraScanner(this);
                });
            });
            
            // 1. Image Upload Preview Handler
            const fileInput = document.getElementById('product-image-input');
            const fileNameSpan = document.getElementById('file-name');
            const previewWrapper = document.getElementById('image-preview-wrapper');
            const previewImg = document.getElementById('image-preview');

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        fileNameSpan.textContent = '📄 ' + file.name;
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            previewWrapper.classList.remove('hidden');
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            // 2. Real-time Discount Percentage Calculator
            const origPriceInput = document.getElementById('original_price');
            const discPriceInput = document.getElementById('discount_price');
            const discBadge = document.getElementById('discount-badge');

            function calculateDiscount() {
                const orig = parseFloat(origPriceInput.value) || 0;
                const disc = parseFloat(discPriceInput.value) || 0;

                if (orig > 0 && disc > 0 && disc < orig) {
                    const percentage = Math.round(((orig - disc) / orig) * 100);
                    discBadge.textContent = `Hemat ${percentage}%`;
                    discBadge.classList.remove('hidden');
                } else {
                    discBadge.classList.add('hidden');
                }
            }

            if (origPriceInput && discPriceInput) {
                origPriceInput.addEventListener('input', calculateDiscount);
                discPriceInput.addEventListener('input', calculateDiscount);
            }

            // 3. Dynamic Live Filter untuk Katalog Menu
            const searchCatalogInput = document.getElementById('search-catalog');
            if (searchCatalogInput) {
                searchCatalogInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase();
                    const items = document.querySelectorAll('.catalog-item');

                    items.forEach(item => {
                        const title = item.querySelector('.product-title')?.textContent.toLowerCase() || '';
                        if (title.includes(query)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }

            // 4. Dynamic Live Filter untuk Pesanan Masuk
            const searchOrdersInput = document.getElementById('search-orders');
            if (searchOrdersInput) {
                searchOrdersInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase();
                    const items = document.querySelectorAll('.order-item');

                    items.forEach(item => {
                        const customerName = item.querySelector('.order-customer')?.textContent.toLowerCase() || '';
                        const bookingCode = item.querySelector('.order-code')?.textContent.toLowerCase() || '';

                        if (customerName.includes(query) || bookingCode.includes(query)) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }
        });

        // 5. Alert Dismiss Handler
        function dismissAlert(button) {
            const alertElement = button.closest('.alert-dismissible');
            if (alertElement) {
                alertElement.style.opacity = '0';
                setTimeout(() => alertElement.remove(), 300);
            }
        }
    </script>
@include('components.live-clock')
</body>
</html>
