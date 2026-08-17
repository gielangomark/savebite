<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ $product->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen p-4" x-data="{ quantity: {{ request('quantity', 1) }} }">

    <div class="max-w-md mx-auto bg-white rounded-3xl shadow-xl border border-slate-100 p-6 my-6">
        <a href="{{ route('food.detail', $product->id) }}" class="inline-flex items-center text-xs text-emerald-700 font-bold mb-4 hover:underline">
            &larr; Kembali ke Detail
        </a>

        <h1 class="text-2xl font-black text-slate-800 mb-1">Checkout Pesanan</h1>
        <p class="text-xs text-slate-500 mb-5">Lengkapi data untuk membuat kode tiket booking makanan.</p>

        <!-- Product Summary Box -->
        <div class="flex gap-4 bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80 mb-6">
            <img src="{{ $product->image_url }}" class="w-16 h-16 rounded-xl object-cover">
            <div>
                <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">
                    {{ $product->merchant->name }}
                </span>
                <h2 class="font-bold text-sm text-slate-800 mt-1">{{ $product->title }}</h2>
                <p class="text-emerald-700 font-black text-base mt-0.5">Rp {{ number_format($product->discount_price) }}</p>
            </div>
        </div>

        <form action="{{ route('checkout.process') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" x-model="quantity">

            <div>
                <label class="block text-xs font-extrabold text-slate-700 mb-1">Nama Lengkap Pemesan</label>
                <input type="text" name="customer_name" value="{{ old('customer_name', Auth::user()->name) }}" placeholder="Misal: Andi Wijaya" required 
                       class="w-full border border-slate-200 rounded-2xl p-3.5 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition bg-slate-50/50">
            </div>

            <div>
                <label class="block text-xs font-extrabold text-slate-700 mb-1">Nomor WhatsApp Aktif</label>
                <input type="number" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="08123456789" required 
                       class="w-full border border-slate-200 rounded-2xl p-3.5 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition bg-slate-50/50">
            </div>

            <div class="border border-slate-200 rounded-2xl p-3.5 bg-slate-50/50">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-extrabold text-slate-700">Jumlah Porsi</label>
                    <span class="text-xs font-bold text-emerald-700" x-text="quantity + ' porsi'"></span>
                </div>
                <div class="mt-3 flex items-center justify-between gap-3">
                    <button type="button" @click="if (quantity > 1) quantity--" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-lg font-bold text-slate-700">-</button>
                    <span class="font-black text-slate-800 text-lg" x-text="quantity"></span>
                    <button type="button" @click="if (quantity < {{ $product->stock }}) quantity++" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-lg font-bold text-slate-700">+</button>
                </div>
            </div>

            <!-- Modern QRIS Box -->
            <div class="border-t border-slate-100 pt-5 mt-2">
                <label class="block text-xs font-extrabold text-slate-700 mb-2">Metode Pembayaran Instant</label>
                <div class="bg-gradient-to-br from-emerald-900 to-teal-900 text-white p-5 rounded-3xl text-center shadow-lg relative overflow-hidden">
                    <p class="text-xs text-emerald-300 font-semibold mb-1">Scan QRIS Bebas Biaya Admin</p>
                    <p class="text-xl font-black text-lime-300 mb-3" x-text="'Rp ' + (quantity * {{ $product->discount_price }}).toLocaleString('id-ID')"></p>
                    
                    <div class="bg-white p-3 rounded-2xl inline-block shadow-inner">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=SurplusBiteQRISDummy" class="w-36 h-36 mx-auto rounded-lg">
                    </div>
                    <p class="text-[10px] text-emerald-200/80 mt-2">Tunjukkan bukti transaksi atau tiket saat pengambilan.</p>
                </div>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-sm py-4 rounded-2xl shadow-xl shadow-emerald-600/30 transition transform active:scale-98">
                Konfirmasi & Ambil Tiket Digital ➔
            </button>
        </form>
    </div>

</body>
</html>