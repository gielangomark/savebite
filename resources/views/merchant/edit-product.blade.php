<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu - Save Bite</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-emerald-50/30 to-teal-50/40 text-slate-800 min-h-screen py-10 antialiased">

    <div class="max-w-2xl mx-auto px-4">
        
        <!-- Top Action Navigation -->
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('merchant.dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-emerald-700 bg-white px-4 py-2.5 rounded-xl border border-slate-200/80 shadow-sm transition duration-200 active:scale-95">
                ← Kembali ke Dashboard
            </a>
            <span class="text-xs font-black text-emerald-800 bg-emerald-100/80 px-3 py-1 rounded-full border border-emerald-200">
                Menu ID: #{{ $product->id }}
            </span>
        </div>

        <!-- Validation Errors Alert -->
        @if ($errors->any())
            <div class="mb-6 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm font-bold text-amber-800 space-y-1.5 shadow-md">
                <div class="pb-1 border-b border-amber-200/60 text-xs font-black uppercase tracking-wider text-amber-900">
                    ⚠️ Terjadi Kesalahan Input:
                </div>
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2 text-xs font-semibold text-amber-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Card Form Edit -->
        <div class="bg-white rounded-[28px] border border-slate-200/90 p-6 sm:p-8 shadow-xl shadow-slate-200/50">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-lg shadow-inner">
                    ✏️
                </div>
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">Edit Informasi Menu</h1>
                    <p class="text-xs font-semibold text-slate-400">Perbarui detail porsi makanan hemat Anda.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('merchant.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Judul Menu -->
                <div>
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 mb-1.5">Judul Menu</label>
                    <input type="text" name="title" value="{{ old('title', $product->title) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 outline-none transition" required>
                </div>

                <!-- Jam, Lokasi & Stok Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 mb-1.5">Waktu Pengambilan</label>
                        <div class="grid grid-cols-2 gap-2"><input type="time" name="pickup_start_time" value="{{ old('pickup_start_time', $product->pickup_start_time ? \Carbon\Carbon::parse($product->pickup_start_time)->format('H:i') : '') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" required><input type="time" name="pickup_end_time" value="{{ old('pickup_end_time', $product->pickup_end_time ? \Carbon\Carbon::parse($product->pickup_end_time)->format('H:i') : '') }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3" required></div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 mb-1.5">Tempat Pengambilan</label>
                        <input type="text" name="pickup_location" value="{{ old('pickup_location', $product->pickup_location) }}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-900 mb-1.5">Stok Porsi</label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 outline-none transition" required>
                    </div>
                </div>

                <!-- Harga Asli & Harga Diskon Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500 mb-1.5">Harga Asli (Rp)</label>
                        <input type="number" name="original_price" value="{{ old('original_price', $product->original_price) }}" min="1" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 outline-none transition" required>
                    </div>
                    <div>
                        <label class="block text-[11px] font-extrabold uppercase tracking-wider text-emerald-700 mb-1.5">Harga Diskon (Rp)</label>
                        <input type="number" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}" min="1" class="w-full rounded-xl border border-emerald-300 bg-emerald-50/30 px-4 py-3 text-sm font-bold text-emerald-900 focus:bg-white focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 outline-none transition" required>
                    </div>
                </div>

                <!-- Gambar Saat Ini & Upload Baru -->
                <div class="space-y-2 pt-2">
                    <label class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-500">Foto Makanan Saat Ini</label>
                    <div class="flex items-center gap-4 p-3 rounded-2xl bg-slate-50 border border-slate-200">
                        <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="w-16 h-16 object-cover rounded-xl border border-slate-200 shadow-sm shrink-0">
                        <div class="text-xs">
                            <p class="font-bold text-slate-700">Ganti Foto Baru (Opsional)</p>
                            <p class="text-slate-400 font-medium mt-0.5">Biarkan kosong jika tidak ingin mengubah foto.</p>
                        </div>
                    </div>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs font-semibold text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-extrabold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-slate-200 rounded-xl bg-slate-50">
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center gap-3 pt-6 border-t border-slate-100">
                    <button type="submit" class="flex-1 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 px-4 py-3.5 text-sm font-black text-white shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/50 transition duration-200 active:scale-[0.98]">
                        💾 Simpan Perubahan
                    </button>
                    <a href="{{ route('merchant.dashboard') }}" class="rounded-2xl border border-slate-200 bg-slate-100 hover:bg-slate-200 px-5 py-3.5 text-sm font-bold text-slate-600 transition duration-200">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

@include('components.live-clock')
</body>
</html>
