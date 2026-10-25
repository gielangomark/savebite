<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->title }} - Save Bite</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                <span class="font-extrabold text-slate-800 text-sm tracking-tight">Save Bite</span>
            </div>
        </div>
    </nav>

    <!-- Main Detail Container -->
    <main class="max-w-4xl mx-auto px-4 py-8 w-full space-y-8">

        <!-- Notifikasi Session & Validation Errors -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>✅ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>⚠️ {{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl text-xs font-semibold space-y-1 shadow-sm">
                @foreach($errors->all() as $error)
                    <p>⚠️ {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Card Utama Produk -->
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
                        Diskon {{ $product->original_price > 0 ? round((($product->original_price - $product->discount_price) / $product->original_price) * 100) : 0 }}%
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
                        <div class="w-full">
                            <div class="flex items-center justify-between">
                                <h3 class="font-extrabold text-slate-900 text-sm">{{ $product->merchant->name ?? 'Merchant' }}</h3>
                                <div class="flex items-center gap-1 text-xs font-bold text-amber-800 bg-amber-100/80 px-2 py-0.5 rounded-lg">
                                    <span>⭐</span>
                                    <span>{{ $product->reviews_avg_rating ? number_format($product->reviews_avg_rating, 1) : 'Baru' }}</span>
                                </div>
                            </div>
                            
                            <!-- Lokasi pengambilan sesuai ketikan merchant di produk -->
                            <p class="text-xs text-slate-700 font-semibold mt-1 flex items-start gap-1">
                                <span>📍</span>
                                <span>Lokasi Ambil: <strong class="text-slate-900 font-bold">{{ $product->pickup_location }}</strong></span>
                            </p>

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
                            <span x-text="'(Rp ' + (quantity * {{ $product->discount_price }}).toLocaleString('id-ID') + ')'"></span>
                        </button>
                    </form>
                @endauth
            </div>
        </div>

        <!-- Section Ulasan & Rating -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl p-6 md:p-8 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                        <span>⭐ Ulasan Pembeli</span>
                        <span class="text-xs font-normal text-slate-500">({{ $product->reviews_count ?? $product->reviews->count() }} ulasan)</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Penilaian transparan dari penyelamat makanan sebelumnya.</p>
                </div>
                
                <div class="text-right">
                    <span class="text-2xl font-black text-amber-500">
                        {{ $product->reviews_avg_rating ? number_format($product->reviews_avg_rating, 1) : '0.0' }}
                    </span>
                    <span class="text-xs text-slate-400 font-bold">/ 5.0</span>
                </div>
            </div>

            <!-- Form Ulasan -->
            @if(isset($eligibleOrder) && $eligibleOrder)
                <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-extrabold text-emerald-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span>✍️ Tulis Ulasan Anda</span>
                        </h4>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">Kode: {{ $eligibleOrder->booking_code }}</span>
                    </div>
                    <p class="text-xs text-emerald-800">
                        Anda telah menyelesaikan pesanan ini! Bagikan pengalaman rasa & kualitas porsi yang Anda dapatkan.
                    </p>

                    <form action="{{ route('review.store') }}" method="POST" x-data="{ rating: 5 }" class="space-y-3 pt-1">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $eligibleOrder->id }}">
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="rating" :value="rating">

                        <!-- Rating Stars Selector -->
                        <div class="flex items-center gap-1">
                            <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                <button type="button" @click="rating = star" class="text-2xl focus:outline-none transition-transform active:scale-125 cursor-pointer">
                                    <span :class="star <= rating ? 'text-amber-400' : 'text-slate-300'">★</span>
                                </button>
                            </template>
                            <span class="text-xs font-bold text-amber-800 ml-2" x-text="rating + ' dari 5 Bintang'"></span>
                        </div>

                        <!-- Comment Textarea -->
                        <textarea name="comment" rows="3" placeholder="Ceritakan kesegaran makanan, pelayanan merchant, atau rasanya (opsional)..." class="w-full rounded-xl border border-slate-200 p-3 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none shadow-sm bg-white"></textarea>

                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-md transition transform active:scale-95 cursor-pointer">
                            Kirim Ulasan Makanan
                        </button>
                    </form>
                </div>
            @endif

            <!-- List Ulasan -->
            <div class="space-y-4">
                @forelse($product->reviews as $review)
                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-100 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px] flex items-center justify-center">
                                    {{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}
                                </div>
                                <span class="text-xs font-bold text-slate-800">{{ $review->user->name ?? 'Pengguna' }}</span>
                            </div>
                            
                            <div class="flex items-center gap-1 text-xs text-amber-400 font-bold">
                                @for($i = 1; $i <= 5; $i++)
                                    <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                        </div>

                        @if($review->comment)
                            <p class="text-xs text-slate-600 leading-relaxed pl-9">
                                "{{ $review->comment }}"
                            </p>
                        @endif

                        <div class="text-[10px] text-slate-400 text-right">
                            {{ $review->created_at ? $review->created_at->diffForHumans() : '' }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        <p class="text-xs text-slate-400 font-medium">Belum ada ulasan untuk makanan ini.</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Jadilah penyelamat makanan pertama yang memberikan penilaian!</p>
                    </div>
                @endforelse
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-6 text-center text-xs border-t border-slate-800 mt-12">
        <p class="font-medium text-slate-300">🌱 Save Bite — Save Food, Save Planet</p>
    </footer>

@include('components.live-clock')
</body>
</html>