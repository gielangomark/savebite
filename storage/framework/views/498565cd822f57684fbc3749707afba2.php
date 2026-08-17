<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merchant Dashboard - SurplusBite</title>
    
    <!-- Fonts & Tailwind CSS -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

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
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white text-lg shadow-md shadow-emerald-600/30 font-black">
                    🌱
                </div>
                <div>
                    <span class="text-lg font-black bg-gradient-to-r from-emerald-800 via-teal-700 to-emerald-900 bg-clip-text text-transparent tracking-tight block leading-none">SurplusBite</span>
                    <span class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-emerald-600">Merchant Center</span>
                </div>
            </div>

            <!-- Profile & Actions -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-emerald-50/80 border border-emerald-200/80 shadow-inner">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-600 text-white flex items-center justify-center text-xs font-black uppercase shadow-sm">
                        <?php echo e(substr(auth()->user()->name, 0, 2)); ?>

                    </div>
                    <span class="text-xs font-bold text-emerald-950 max-w-[140px] truncate"><?php echo e(auth()->user()->name); ?></span>
                </div>

                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
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
                <!-- Total Menu Card -->
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 border border-emerald-400/50 rounded-2xl p-4 shadow-lg shadow-emerald-600/20 min-w-[140px] text-white transform hover:-translate-y-1 transition duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase tracking-wider text-emerald-100">Total Menu</p>
                        <span class="text-base">🍲</span>
                    </div>
                    <p class="text-2xl font-black mt-1"><?php echo e($products->count()); ?> <span class="text-xs text-emerald-100 font-semibold">item</span></p>
                </div>

                <!-- Order Masuk Card -->
                <div class="bg-gradient-to-br from-amber-500 to-orange-500 border border-amber-400/50 rounded-2xl p-4 shadow-lg shadow-amber-500/20 min-w-[140px] text-white transform hover:-translate-y-1 transition duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black uppercase tracking-wider text-amber-100">Order Masuk</p>
                        <span class="text-base">🛍️</span>
                    </div>
                    <p class="text-2xl font-black mt-1"><?php echo e($orders->count()); ?> <span class="text-xs text-amber-100 font-semibold">pesanan</span></p>
                </div>
            </div>
        </div>

        <!-- Alerts Notifications -->
        <?php if(session('status')): ?>
            <div class="mb-6 rounded-2xl border border-emerald-400 bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600 p-4 text-sm font-bold text-white flex items-center gap-3 shadow-lg shadow-emerald-500/20">
                <div class="w-7 h-7 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                    ✓
                </div>
                <span><?php echo e(session('status')); ?></span>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mb-6 rounded-2xl border border-rose-300 bg-rose-50 p-4 text-sm font-bold text-rose-800 space-y-1.5 shadow-md">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span><?php echo e($error); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

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

                    <form method="POST" action="<?php echo e(route('merchant.products.store')); ?>" enctype="multipart/form-data" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        
                        <!-- Judul Menu -->
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 mb-1.5">Judul Menu</label>
                            <input type="text" name="title" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-3 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 transition outline-none" placeholder="Contoh: Nasi Ayam Geprek Extra Sambal" required>
                        </div>

                        <!-- Jam Ambil, Lokasi & Stok Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 mb-1.5">Waktu Pengambilan</label>
                                <input type="text" name="pickup_time" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-3 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 transition outline-none" placeholder="Contoh: 17:00 - 19:00 WIB" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 mb-1.5">Tempat Pengambilan</label>
                                <input type="text" name="pickup_location" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-3 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 transition outline-none" placeholder="Contoh: Depan Kantin Utama" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 mb-1.5">Stok Porsi</label>
                                <input type="number" name="stock" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-3 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 transition outline-none" placeholder="10" required>
                            </div>
                        </div>

                        <!-- Harga Asli & Harga Diskon Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Harga Asli (Rp)</label>
                                <input type="number" name="original_price" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-3 text-sm font-semibold text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 transition outline-none" placeholder="20000" required>
                            </div>
                            <div>
                                <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-700 mb-1.5">Harga Diskon Surplus (Rp)</label>
                                <input type="number" name="discount_price" class="w-full rounded-xl border border-emerald-300 bg-emerald-50/30 px-4 py-3 text-sm font-bold text-emerald-900 placeholder:text-emerald-400 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 transition outline-none" placeholder="8000" required>
                            </div>
                        </div>

                        <!-- Upload Foto Makanan -->
                        <div>
                            <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Upload Foto Makanan</label>
                            <div class="relative">
                                <input type="file" name="image" accept="image/*" class="hidden" id="product-image-input">
                                <label for="product-image-input" class="w-full rounded-xl border-2 border-dashed border-slate-300 bg-slate-50/70 px-4 py-4 text-sm font-semibold text-slate-600 placeholder:text-slate-400 cursor-pointer hover:border-emerald-400 hover:bg-emerald-50/30 transition flex items-center justify-center gap-2">
                                    <span class="text-lg">📸</span>
                                    <span id="file-name">Pilih file atau drag & drop</span>
                                </label>
                            </div>
                        </div>
                        
                        <script>
                            const fileInput = document.getElementById('product-image-input');
                            const fileNameSpan = document.getElementById('file-name');
                            
                            fileInput.addEventListener('change', function(e) {
                                if (this.files && this.files[0]) {
                                    fileNameSpan.textContent = this.files[0].name;
                                }
                            });
                        </script>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full mt-3 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-700 hover:to-teal-800 px-4 py-3.5 text-sm font-black text-white shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/50 transition duration-200 active:scale-[0.98] flex items-center justify-center gap-2">
                            <span>🚀 Simpan Menu Baru</span>
                        </button>
                    </form>
                </section>

                <!-- SECTION 2: Katalog Menu Saya -->
                <section class="rounded-[28px] bg-white border border-slate-200/90 p-6 sm:p-7 shadow-xl shadow-slate-200/50">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2.5">
                            <span class="text-xl">🍱</span>
                            <h2 class="text-lg font-black text-slate-900 tracking-tight">Katalog Menu Saya</h2>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black">
                            <?php echo e($products->count()); ?> Terdaftar
                        </span>
                    </div>

                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="group flex flex-col sm:flex-row items-start sm:items-center gap-4 rounded-2xl border border-slate-200/80 bg-slate-50/60 p-4 hover:bg-emerald-50/40 hover:border-emerald-400 transition duration-200 hover:shadow-md">
                                <!-- Image Thumbnail -->
                                <div class="relative w-full sm:w-24 h-32 sm:h-24 rounded-xl overflow-hidden bg-slate-200 shrink-0 shadow-sm">
                                    <img src="<?php echo e($product->image_url ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=300'); ?>" 
                                         alt="<?php echo e($product->title); ?>" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-slate-900/80 backdrop-blur-sm text-white text-[10px] font-extrabold">
                                        ID: #<?php echo e($product->id); ?>

                                    </span>
                                </div>

                                <!-- Info Content -->
                                <div class="flex-1 min-w-0 w-full">
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="font-black text-slate-900 truncate text-base group-hover:text-emerald-900 transition"><?php echo e($product->title); ?></h3>
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-500 text-white text-xs font-black shadow-sm shrink-0">
                                            <span>📦</span> <?php echo e($product->stock); ?> stok
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2.5 mt-2">
                                        <span class="text-lg font-black text-emerald-700">Rp <?php echo e(number_format($product->discount_price)); ?></span>
                                        <?php if($product->original_price): ?>
                                            <span class="text-xs text-slate-400 line-through font-semibold">Rp <?php echo e(number_format($product->original_price)); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex items-center gap-1.5 mt-2 text-xs font-bold text-slate-500 bg-white/80 px-2.5 py-1 rounded-lg border border-slate-200/60 w-fit">
                                        <span class="text-amber-500">⏰</span>
                                        <span>Ambil: <?php echo e($product->pickup_time); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="rounded-2xl border-2 border-dashed border-emerald-200 bg-gradient-to-b from-slate-50 to-emerald-50/30 px-4 py-12 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                                    🍲
                                </div>
                                <p class="text-base font-black text-slate-800">Belum ada menu yang ditambahkan</p>
                                <p class="text-xs font-semibold text-slate-400 mt-1 max-w-xs mx-auto">Gunakan formulir di atas untuk mendaftarkan porsi makanan pertama Anda.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

            </div>

            <!-- RIGHT COLUMN (Order Masuk & Redeem Form) -->
            <div class="lg:col-span-5 space-y-8">
                
                <section class="rounded-[28px] bg-white border border-slate-200/90 p-6 sm:p-7 shadow-xl shadow-slate-200/50 sticky top-24">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="text-xl">⚡</span>
                            <div>
                                <h2 class="text-lg font-black text-slate-900 tracking-tight">Pesanan Masuk</h2>
                                <p class="text-xs text-slate-400 font-medium">Validasi penukaran voucher pembeli.</p>
                            </div>
                        </div>
                        <span class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-gradient-to-r from-amber-500 to-orange-500 text-white text-[10px] font-black uppercase tracking-wider shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-white animate-ping"></span> Live
                        </span>
                    </div>

                    <div class="space-y-4">
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 space-y-3 relative overflow-hidden transition hover:border-emerald-400 hover:shadow-md">
                                
                                <!-- Card Header: Product & Status -->
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 bg-emerald-100/80 px-2 py-0.5 rounded-md inline-block">
                                            <?php echo e($order->product->title ?? 'Menu Dihapus'); ?>

                                        </span>
                                        <h3 class="font-black text-slate-900 text-base mt-1.5"><?php echo e($order->customer_name); ?></h3>
                                    </div>
                                    
                                    <!-- Status Badge -->
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shrink-0 shadow-sm <?php echo e($order->status === 'served' ? 'bg-emerald-500 text-white' : 'bg-gradient-to-r from-amber-500 to-orange-500 text-white animate-pulse'); ?>">
                                        <?php echo e($order->status === 'served' ? '✓ Selesai' : '⏳ Menunggu'); ?>

                                    </span>
                                </div>

                                <!-- Details List -->
                                <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 space-y-2 text-xs shadow-inner">
                                    <div class="flex items-center justify-between text-slate-600">
                                        <span>No. WhatsApp:</span>
                                        <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded">📱 <?php echo e($order->customer_phone); ?></span>
                                    </div>
                                    <div class="flex items-center justify-between text-slate-600">
                                        <span>Jumlah Porsi:</span>
                                        <span class="font-bold text-slate-900"><?php echo e($order->quantity); ?> porsi</span>
                                    </div>
                                    <div class="flex items-center justify-between text-slate-600">
                                        <span>Total Tagihan:</span>
                                        <span class="font-black text-emerald-700 text-sm">Rp <?php echo e(number_format($order->total_price)); ?></span>
                                    </div>
                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                        <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Kode Booking:</span>
                                        <span class="font-black text-sm tracking-widest text-emerald-900 bg-emerald-100 px-2.5 py-1 rounded-lg border border-emerald-300">
                                            <?php echo e($order->booking_code); ?>

                                        </span>
                                    </div>
                                </div>

                                <!-- Redeem Form (Show only if not served yet) -->
                                <?php if($order->status !== 'served'): ?>
                                    <form method="POST" action="<?php echo e(route('merchant.orders.redeem', $order->id)); ?>" class="pt-1">
                                        <?php echo csrf_field(); ?>
                                        <div class="flex items-center gap-2">
                                            <input type="text" 
                                                   name="code" 
                                                   value="<?php echo e(old('code')); ?>" 
                                                   placeholder="Masukkan kode unik..." 
                                                   class="flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-bold text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition" 
                                                   required>
                                            <button type="submit" 
                                                    class="rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 px-4 py-2.5 text-xs font-black text-white transition active:scale-95 shadow-md shadow-emerald-600/20 shrink-0">
                                                Redeem
                                            </button>
                                        </div>
                                    </form>
                                <?php endif; ?>

                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="rounded-2xl border-2 border-dashed border-amber-200 bg-gradient-to-b from-slate-50 to-amber-50/30 px-4 py-12 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-3xl mx-auto mb-3 shadow-inner">
                                    🛒
                                </div>
                                <p class="text-base font-black text-slate-800">Belum ada pesanan masuk</p>
                                <p class="text-xs font-semibold text-slate-400 mt-1 max-w-xs mx-auto">Pesanan dari pembeli akan otomatis muncul di sini secara real-time.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

            </div>

        </div>
    </main>

</body>
</html><?php /**PATH C:\xampp\htdocs\surplusbite\resources\views/merchant/dashboard.blade.php ENDPATH**/ ?>