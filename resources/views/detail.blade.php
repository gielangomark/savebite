<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->title }} - SurplusBite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col justify-between" x-data="{ quantity: 1 }">

    <!-- Header Navigation -->
    <nav class="sticky top-0 z-40 glass border-b border-emerald-100 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 py-3.5 flex items-center justify-between">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-emerald-700 transition bg-white border border-slate-200 px-3.5 py-2 rounded-xl shadow-sm">
                <span>← Kembali ke Katalog</span>
            </a>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-600 flex items-center justify-center text-white text-xs font-black">🌱</div>
                <span class="font-extrabold text-slate-800 text-sm tracking-tight">SurplusBite</span>
            </div>
        </div>
    </nav>

    <!-- Main Detail Container -->
    <main class="max-w-4xl mx-auto px-4 py-8 w-full">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-2">
            
            <!-- Left: Product Image & Badges -->
            <div class="relative h-72 md:h-full min-h-[320px] bg-slate-900">
                <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                
                <div class="absolute top-4 left-4 flex gap-2">
                    <span class="bg-emerald-600 text-white text-xs font-extrabold px-3 py-1 rounded-full shadow-md flex items-center gap-1">
                        🌱 Hemat 0.8 kg CO2
                    </span>
                </div>

                <div class="absolute top-4 right-4">
                    <span class="bg-red-600 text-white text-xs font-black px-3 py-1 rounded-full shadow-lg">
                        Diskon {{ round((($product->original_price - $product->discount_price) / $product->original_price) * 100) }}%
                    </span>
                </div>

                <div class="absolute bottom-4 left-4 right-4 text-white">
                    <div class="flex items-center gap-2 text-xs text-emerald-300 font-medium mb-1">
                        <span>📦 Tersisa {{ $product->stock }} porsi lagi</span>
                    </div>
                    <h1 class="text-2xl font-black leading-tight drop-shadow-md">{{ $product->title }}</h1>
                </div>
            </div>

            <!-- Right: Details & Order Box -->
            <div class="p-6 md:p-8 flex flex-col justify-between space-y-6">
                <div class="space-y-5">
                    
                    <!-- Merchant Box -->
                    <div class="p-4 bg-emerald-50/60 rounded-2xl border border-emerald-100/80 flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shrink-0 shadow-sm">
                            🏪
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-sm">{{ $product->merchant->name }}</h3>
                            <p class="text-xs text-slate-600 mt-0.5">📍 {{ $product->merchant->location ?? 'Lokasi' }}</p>
                            <span class="inline-block mt-2 text-[11px] font-bold text-amber-800 bg-amber-100/80 px-2.5 py-0.5 rounded-md border border-amber-200">
                                ⏰ Jam Ambil: {{ $product->pickup_time }}
                            </span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Deskripsi Makanan</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            {{ $product->description }} Makanan ini diolah segar hari ini dan masih dalam kondisi kualitas sangat layak konsumsi. Bantu kurangi pemborosan makanan dengan menyelamatkan porsi ini!
                        </p>
                    </div>

                    <!-- Price Calculation -->
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-xs text-slate-400 line-through font-medium">Rp {{ number_format($product->original_price) }} / porsi</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black text-emerald-700">Rp {{ number_format($product->discount_price) }}</span>
                            <span class="text-xs text-slate-500 font-medium">/ porsi</span>
                        </div>
                    </div>

                    <!-- Quantity Selector -->
                    <div class="flex items-center justify-between bg-slate-50 p-3 rounded-2xl border border-slate-100">
                        <span class="text-xs font-bold text-slate-700">Jumlah Porsi</span>
                        <div class="flex items-center gap-3">
                            <button @click="if (quantity > 1) quantity--" class="w-8 h-8 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-slate-100 active:scale-95 transition">-</button>
                            <span x-text="quantity" class="font-extrabold text-slate-800 text-sm w-4 text-center"></span>
                            <button @click="if (quantity < {{ $product->stock }}) quantity++" class="w-8 h-8 bg-white border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-slate-100 active:scale-95 transition">+</button>
                        </div>
                    </div>

                </div>

                <!-- Action Button -->
                @guest
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-sm py-4 rounded-2xl shadow-lg shadow-emerald-600/25 transition transform active:scale-[0.98] flex items-center justify-center gap-2">
                        <span>🔐 Login dulu untuk pesan</span>
                        <span>(Rp {{ number_format($product->discount_price) }})</span>
                    </a>
                @endguest

                @auth
                    <form action="{{ route('checkout', $product->id) }}" method="GET" class="w-full">
                        <input type="hidden" name="quantity" x-model="quantity" value="1">
                        <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-sm py-4 rounded-2xl shadow-lg shadow-emerald-600/25 transition transform active:scale-[0.98] flex items-center justify-center gap-2">
                            <span>⚡ Selamatkan Porsi Ini</span>
                            <span x-text="'(Rp ' + (quantity * {{ $product->discount_price }}).toLocaleString('id-ID') + ')'" ></span>
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-6 text-center text-xs border-t border-slate-800">
        <p class="font-medium text-slate-300">🌱 SurplusBite — Save Food, Save Planet</p>
    </footer>

</body>
</html>