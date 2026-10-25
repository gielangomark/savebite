<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Save Bite - Save Food, Save Planet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(10deg); }
        }
        @keyframes floatReverse {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(12px) rotate(-8deg); }
        }
        @keyframes spinSlow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.1); }
        }

        .animate-float { animation: float 4s ease-in-out infinite; }
        .animate-float-reverse { animation: floatReverse 5s ease-in-out infinite; }
        .animate-spin-slow { animation: spinSlow 25s linear infinite; }
        .animate-glow { animation: pulseGlow 3s ease-in-out infinite; }

        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .glass-dark {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        /* ===== FIXES ===== */

        /* 1. Search bar wrapper — cegah melar, pastikan shrink benar */
        .search-wrapper {
            position: relative;
            width: 100%;
            min-width: 0;
            flex-shrink: 1;
        }
        @media (min-width: 640px) {
            .search-wrapper {
                width: 280px;
                flex-shrink: 0;
            }
        }

        /* 2. Search icon — posisi absolut konsisten */
        .search-icon {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0.75rem;
            display: flex;
            align-items: center;
            pointer-events: none;
            color: #94a3b8;
            z-index: 10;
        }

        /* 3. Search input — padding kiri konsisten via CSS, bukan inline style */
        .search-input {
            width: 100%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #1e293b;
            padding: 0.625rem 1rem 0.625rem 2.5rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            box-sizing: border-box;
        }
        .search-input::placeholder { color: #94a3b8; }
        .search-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.15);
        }

        /* 4. Catalog header row — align top, wrap properly */
        .catalog-header {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            width: 100%;
        }
        @media (min-width: 640px) {
            .catalog-header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                gap: 1rem;
            }
        }

        /* 5. Heading teks — pastikan tidak wrap aneh */
        .catalog-heading {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.125rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.3;
            white-space: normal;
        }
        @media (min-width: 640px) {
            .catalog-heading { font-size: 1.25rem; }
        }
        .catalog-heading .emoji-icon {
            flex-shrink: 0;
            font-size: 1.25rem;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans min-h-screen flex flex-col justify-between" 
      x-data="{ 
          showIntro: !sessionStorage.getItem('surplus_intro_seen'), 
          progress: 0, 
          loadingText: 'Menyiapkan porsi makanan surplus...',
          skipIntro() {
              this.showIntro = false;
              sessionStorage.setItem('surplus_intro_seen', 'true');
          }
      }" 
      x-init="
          if (showIntro) {
              let timer = setInterval(() => {
                  if (progress < 100) {
                      progress += 4;
                      if (progress === 28) loadingText = 'Menghitung potensi penekanan emisi CO2...';
                      if (progress === 60) loadingText = 'Membaca data merchant & kantin terdekat...';
                      if (progress === 88) loadingText = 'Siap! Selamatkan makanan & bumi sekarang.';
                  } else {
                      clearInterval(timer);
                      setTimeout(() => { skipIntro(); }, 300);
                  }
              }, 50);
          }
      ">

    <!-- ========================================== -->
    <!-- 🌍 FULLSCREEN ECO INTRO / SPLASH SCREEN    -->
    <!-- ========================================== -->
    <div x-show="showIntro" 
         x-cloak
         x-transition:leave="transition ease-in duration-500 transform"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-105"
         class="fixed inset-0 z-[100] bg-gradient-to-br from-slate-950 via-emerald-950 to-teal-950 text-white flex flex-col justify-between items-center p-6 overflow-hidden">
        
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl animate-glow pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-teal-500/20 rounded-full blur-3xl animate-glow pointer-events-none"></div>

        <div class="absolute top-12 left-10 text-3xl animate-float opacity-40 pointer-events-none">🍃</div>
        <div class="absolute bottom-20 left-12 text-3xl animate-float-reverse opacity-40 pointer-events-none">🌾</div>

        <div class="w-full max-w-2xl flex justify-between items-center relative z-10 pt-2">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full glass-dark border border-emerald-500/30 text-emerald-300 text-xs font-bold tracking-wider uppercase">
                <span>🌱 SAVE BITE ECO MOVEMENT</span>
            </div>
            <button @click="skipIntro()" 
                    class="text-xs font-semibold text-slate-300 hover:text-white bg-white/10 hover:bg-white/20 px-4 py-2 rounded-full border border-white/10 backdrop-blur-md transition flex items-center gap-1 cursor-pointer">
                <span>Lewati</span>
                <span>➔</span>
            </button>
        </div>

        <div class="flex flex-col items-center text-center max-w-2xl mx-auto my-auto relative z-10 space-y-6">
            <div class="relative flex items-center justify-center my-2">
                <div class="absolute w-44 h-44 rounded-full bg-emerald-500/30 blur-2xl animate-pulse"></div>
                <div class="absolute w-40 h-40 rounded-full border-2 border-dashed border-emerald-400/40 animate-spin-slow"></div>
                
                <div class="relative w-36 h-36 rounded-full bg-gradient-to-tr from-teal-600 via-emerald-500 to-lime-400 flex items-center justify-center shadow-[0_0_50px_rgba(16,185,129,0.5)] border-2 border-white/30 transform hover:scale-105 transition duration-500">
                    <svg class="w-24 h-24 text-slate-900/80 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 002.5-2.5V7.865M12 21a9 9 0 100-18 9 9 0 000 18z"></path>
                    </svg>
                </div>
            </div>

            <div class="space-y-3 px-2">
                <h1 class="text-2xl md:text-4xl font-black leading-tight tracking-tight">
                    "Jagalah lingkunganmu dengan tidak membuang makanan tak terjual."
                </h1>
                
                <div class="inline-block pt-1">
                    <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-lime-300 bg-clip-text text-transparent text-sm md:text-lg font-extrabold uppercase tracking-widest drop-shadow-sm">
                        ⚡ Bangkitkan Ekonomi & Kurangi Limbah ⚡
                    </span>
                </div>
            </div>
        </div>

        <div class="w-full max-w-md mx-auto relative z-10 pb-4 space-y-2">
            <div class="flex justify-between items-center text-xs font-medium text-emerald-200/90">
                <span x-text="loadingText" class="truncate max-w-[80%]"></span>
                <span x-text="progress + '%'" class="font-bold text-lime-300 font-mono"></span>
            </div>

            <div class="w-full bg-slate-800/80 rounded-full h-3 p-0.5 border border-white/10 shadow-inner overflow-hidden backdrop-blur-md">
                <div class="bg-gradient-to-r from-emerald-500 via-teal-400 to-lime-400 h-full rounded-full transition-all duration-100 ease-linear shadow-[0_0_12px_rgba(52,211,153,0.8)]"
                     :style="'width: ' + progress + '%'"></div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MAIN NAVBAR                                -->
    <!-- ========================================== -->
    <nav class="w-full glass border-b border-slate-200/80 shadow-sm relative z-40">
        <div class="max-w-6xl mx-auto px-4 py-2.5 flex justify-between items-center">
            
            <a href="{{ route('home') }}" class="flex items-center gap-2 whitespace-nowrap shrink-0 group">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform shrink-0 text-base">
                    🌱
                </div>
                <div class="flex flex-col justify-center gap-1">
                    <span class="text-sm font-black text-slate-800 tracking-tight leading-none">Save Bite</span>
                    <span class="text-[9px] font-semibold text-slate-400 tracking-normal leading-none">Save Food · Save Planet</span>
                </div>
            </a>

            <div class="flex items-center gap-2 shrink-0">
                @guest
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}" 
                       class="text-xs font-bold text-slate-700 hover:text-emerald-700 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-emerald-300 transition bg-white shadow-sm">
                        Masuk
                    </a>
                    <a href="{{ route('register', ['redirect' => url()->current()]) }}" 
                       class="text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 px-3.5 py-1.5 rounded-xl shadow-sm transition active:scale-95">
                        Daftar
                    </a>
                @endguest

                @auth
                    @if(Auth::user()->isMerchant())
                        <a href="{{ route('merchant.dashboard') }}" 
                           class="hidden sm:flex items-center gap-1 text-xs font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200/80 px-2.5 py-1.5 rounded-xl transition shadow-sm">
                            <span>🏪 Dashboard</span>
                        </a>
                    @endif

                    <a href="{{ Route::has('orders.history') ? route('orders.history') : (Route::has('history') ? route('history') : '#') }}" 
                       class="flex items-center gap-1.5 text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3 py-1.5 rounded-xl transition shadow-sm">
                        <span>📜</span>
                        <span>History</span>
                    </a>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center gap-1.5 text-xs font-bold text-slate-800 bg-white hover:bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-200 transition shadow-sm cursor-pointer">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-[10px] font-black">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="max-w-[75px] md:max-w-[110px] truncate">
                                {{ explode(' ', trim(Auth::user()->name))[0] }}
                            </span>
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" 
                             @click.outside="open = false" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-[10px] text-slate-400 font-medium">Masuk sebagai</p>
                                <p class="text-xs font-extrabold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                            </div>

                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">
                                👤 Profil Saya
                            </a>

                            @if(Auth::user()->isMerchant())
                                <a href="{{ route('merchant.dashboard') }}" class="sm:hidden block px-4 py-2 text-xs font-bold text-amber-800 hover:bg-amber-50">
                                    🏪 Dashboard Penjual
                                </a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 transition flex items-center gap-2 mt-1 cursor-pointer">
                                    <span>🚪 Keluar (Logout)</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- HERO BANNER -->
    <section class="relative overflow-hidden bg-gradient-to-br from-emerald-900 via-teal-900 to-slate-900 text-white pt-10 pb-16 px-4">
        <div class="absolute top-6 left-8 text-3xl animate-float opacity-30 pointer-events-none">🍃</div>
        <div class="absolute bottom-6 right-10 text-4xl animate-float-reverse opacity-30 pointer-events-none">🌿</div>
        <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-emerald-500/20 rounded-full blur-3xl animate-glow pointer-events-none"></div>

        <div class="max-w-4xl mx-auto text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-medium mb-4 backdrop-blur-md">
                <span>🌎 Zero Food Waste Mission</span>
            </div>
            
            <h1 class="text-3xl md:text-5xl font-extrabold leading-tight tracking-tight">
                Selamatkan Makanan Lezat,<br>
                <span class="bg-gradient-to-r from-emerald-300 via-teal-200 to-lime-300 bg-clip-text text-transparent">
                    Potong Emisi Karbon Dunia!
                </span>
            </h1>
            
            <p class="text-emerald-100/80 text-sm md:text-base mt-3 max-w-2xl mx-auto leading-relaxed">
                Dapatkan makanan kualitas prima dari kantin & resto sekitar dengan diskon hingga 70% sebelum jam operasional berakhir.
            </p>

            <div class="mt-8 grid grid-cols-3 gap-3 max-w-lg mx-auto">
                <div class="glass border border-white/10 rounded-2xl p-3 text-center text-slate-800 shadow-xl">
                    <p class="text-lg md:text-2xl font-black text-emerald-600">520+</p>
                    <p class="text-[10px] md:text-xs text-slate-500 font-medium">Porsi Saved</p>
                </div>
                <div class="glass border border-white/10 rounded-2xl p-3 text-center text-slate-800 shadow-xl">
                    <p class="text-lg md:text-2xl font-black text-teal-600">1.2 Ton</p>
                    <p class="text-[10px] md:text-xs text-slate-500 font-medium">CO2 Prevented</p>
                </div>
                <div class="glass border border-white/10 rounded-2xl p-3 text-center text-slate-800 shadow-xl">
                    <p class="text-lg md:text-2xl font-black text-lime-600">60%</p>
                    <p class="text-[10px] md:text-xs text-slate-500 font-medium">Avg. Discount</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ALERT ERROR -->
    @if(session('error'))
        <div class="max-w-6xl mx-auto px-4 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl text-xs font-semibold flex items-center justify-between shadow-sm">
                <span>⚠️ {{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-800 font-bold text-lg">&times;</button>
            </div>
        </div>
    @endif

    <!-- KATALOG UTAMA -->
    <main class="max-w-6xl mx-auto px-4 py-8 w-full overflow-hidden">

        <!-- HEADER SECTION & SEARCH BAR — FIXED -->
        <div class="catalog-header">
            <!-- Teks kiri -->
            <div class="min-w-0">
                <h2 class="catalog-heading">
                    <span class="emoji-icon">🍲</span>
                    <span>
                        @if($isFiveStarRecommendation && $recommendationRating !== null)
                            Rekomendasi Makanan untuk Anda
                        @elseif($isFiveStarRecommendation)
                            Makanan Terdekat di Sekitar Anda
                        @else
                            Pilihan Makanan Hari Ini
                        @endif
                    </span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    @if($isFiveStarRecommendation && $recommendationRating !== null)
                        Pilihan lezat dari mitra terbaik kami, direkomendasikan berdasarkan rating dan lokasi terdekat Anda.
                    @elseif($isFiveStarRecommendation)
                        Belum ada ulasan. Menampilkan makanan terdekat terlebih dahulu.
                    @else
                        Stok terbatas! Segera booking sebelum kehabisan jam ambil.
                    @endif
                </p>
            </div>

            <!-- Search bar + GPS button kanan -->
            <div class="flex items-center gap-2 flex-wrap justify-end">
                <button id="gpsButton" 
                        type="button"
                        title="Aktifkan untuk melihat makanan terdekat"
                        class="px-3 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-800 text-xs font-black rounded-xl border border-emerald-300 transition active:scale-95 flex items-center gap-1.5 shadow-sm cursor-pointer whitespace-nowrap">
                    <span id="gpsIcon">📍</span>
                    <span id="gpsText">Lokasi Saya</span>
                </button>

                <!-- Search bar kanan — FIXED -->
                <div class="search-wrapper">
                    <div class="search-icon">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           id="searchInput"
                           placeholder="Cari menu / kantin..." 
                           class="search-input">
                </div>
            </div>
        </div>

        <!-- GRID PRODUK -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($products as $product)
            @php
                $discountPercent = ($product->original_price > 0 && $product->discount_price < $product->original_price)
                    ? round((($product->original_price - $product->discount_price) / $product->original_price) * 100) 
                    : 0;
                $productSearchText = strtolower(trim($product->title . ' ' . ($product->merchant->name ?? '') . ' ' . ($product->pickup_location ?? '')));
            @endphp
            <div class="product-card group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:border-emerald-200 transition-all duration-300 overflow-hidden flex flex-col justify-between" data-search="{{ $productSearchText }}">
                <div>
                    <div class="relative overflow-hidden h-52 bg-slate-900">
                        <img src="{{ $product->image_url }}" 
                             alt="{{ $product->title }}" 
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent pointer-events-none"></div>
                        
                        <div class="absolute top-3 left-3 z-10">
                            <span class="bg-white/90 backdrop-blur-md text-slate-900 text-[11px] font-extrabold px-3 py-1 rounded-full border border-white/60 shadow-md flex items-center gap-1">
                                🏢 {{ $product->merchant->name ?? 'Kantin Partner' }}
                            </span>
                        </div>

                        @if($isFiveStarRecommendation)
                        <div class="absolute top-12 left-3 z-10">
                            <span class="bg-amber-400 text-amber-950 text-[11px] font-black px-3 py-1 rounded-full shadow-md">
                                {{ $product->reviews_avg_rating !== null
                                    ? '⭐ ' . number_format($product->reviews_avg_rating, 1, ',', '.') . ' Bintang'
                                    : '✨ Menu Baru' }}
                            </span>
                        </div>
                        @endif

                        @if($discountPercent > 0)
                        <div class="absolute top-3 right-3 z-10">
                            <span class="bg-gradient-to-r from-red-600 to-amber-500 text-white text-xs font-black px-3 py-1 rounded-full shadow-lg">
                                Save {{ $discountPercent }}%
                            </span>
                        </div>
                        @endif

                        <div class="absolute bottom-3 left-3 right-3 z-10">
                            <h3 class="font-black text-lg text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)] line-clamp-1">
                                {{ $product->title }}
                            </h3>
                        </div>
                    </div>

                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 gap-2">
                            <span class="flex items-center gap-1 font-medium truncate">
                                📍 {{ $product->pickup_location }}
                            </span>
                            <span class="font-semibold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/60 whitespace-nowrap text-[11px]">
                                ⏰ {{ $product->pickup_time }}
                            </span>
                        </div>

                        @if($product->distance_formatted)
                            <div class="flex items-center gap-2 bg-gradient-to-r from-emerald-50 to-teal-50 p-2.5 rounded-xl border border-emerald-200/60">
                                <span class="text-sm font-black text-emerald-700">📏</span>
                                <span class="text-xs font-extrabold text-emerald-800">{{ $product->distance_formatted }} dari lokasi Anda</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between text-[11px]">
                            <div class="flex items-center gap-1.5 font-medium text-emerald-700">
                                <span>🌱 ~0.8 kg CO2</span>
                                <span>•</span>
                                <span class="{{ $product->stock <= 3 ? 'text-red-600 font-bold animate-pulse' : 'text-slate-500' }}">
                                    Sisa {{ $product->stock }} porsi
                                </span>
                            </div>

                            <div class="flex items-center gap-1 bg-amber-50 border border-amber-200/80 px-2 py-0.5 rounded-lg text-amber-800 font-bold">
                                <span>⭐</span>
                                <span>{{ $product->reviews_avg_rating ? number_format($product->reviews_avg_rating, 1) : 'Baru' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 pt-0 flex items-center justify-between border-t border-slate-100 mt-2">
                    <div>
                        <span class="text-xs text-slate-400 line-through font-medium block">Rp {{ number_format($product->original_price, 0, ',', '.') }}</span>
                        <div class="text-xl font-black text-emerald-700 tracking-tight">
                            Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                        </div>
                    </div>
                    <a href="{{ route('food.detail', $product->id) }}" 
                       class="inline-flex items-center gap-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-md transition-all">
                        <span>Detail</span>
                        <span>➔</span>
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-16 bg-white rounded-3xl border border-slate-100 p-8 shadow-sm">
                <div class="text-5xl mb-3">🍃</div>
                <h3 class="text-base font-bold text-slate-700">
                    {{ $isFiveStarRecommendation ? 'Belum Ada Makanan di Sekitar Anda' : 'Belum Ada Makanan Surplus Tersedia' }}
                </h3>
                <p class="text-xs text-slate-400 mt-1">
                    {{ $isFiveStarRecommendation ? 'Coba perbarui lokasi atau cek kembali nanti.' : 'Silakan cek kembali saat jam operasional kantin berakhir.' }}
                </p>
            </div>
            @endforelse

            <div id="searchEmptyState" class="hidden col-span-full text-center py-16 bg-white rounded-3xl border border-slate-100 p-8 shadow-sm">
                <div class="text-5xl mb-3">🔎</div>
                <h3 class="text-base font-bold text-slate-700">Menu tidak ditemukan</h3>
                <p class="text-xs text-slate-400 mt-1">Coba cari nama makanan, kantin, atau lokasi lain.</p>
            </div>
        </div>
    </main>

    @include('components.live-clock', ['flow' => true])

    <!-- ========================================== -->
    <!-- FOOTER                                     -->
    <!-- ========================================== -->
    <footer style="width:100%; box-sizing:border-box; overflow:hidden; background:#0a0f0d; margin-top:4rem; border-top:1px solid #1a2b1f;">
    <div style="width:100%; max-width:64rem; box-sizing:border-box; margin:0 auto; padding:2.5rem 1rem 1.5rem; display:flex; flex-direction:column; gap:1.5rem;">

        <!-- BRAND ROW -->
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; background:linear-gradient(135deg,#059669,#0d9488); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;">🌱</div>
                <div>
                    <p style="font-size:15px; font-weight:800; color:#f0fdf4; margin:0; letter-spacing:-0.3px; line-height:1;">Save Bite</p>
                    <p style="font-size:10px; color:#4b6353; margin:3px 0 0; line-height:1;">Save Food · Save Planet</p>
                </div>
            </div>
            <div style="display:flex; gap:6px;">
                <span style="font-size:10px; font-weight:800; color:#4ade80; background:#052e16; border:1px solid #166534; padding:4px 10px; border-radius:20px;">🌎 Zero Waste</span>
                <span style="font-size:10px; font-weight:800; color:#2dd4bf; background:#042f2e; border:1px solid #134e4a; padding:4px 10px; border-radius:20px;">♻️ Eco Mission</span>
            </div>
        </div>

        <hr style="border:none; height:1px; background:#1a2b1f; margin:0;">

        <!-- QUOTE CARD -->
        <div style="position:relative; background:#0f1f17; border:1px solid #1e3a29; border-radius:14px; padding:20px 16px 16px;">
            <span style="position:absolute; top:-10px; left:14px; background:#0a0f0d; padding:0 8px; font-size:9px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase; color:#34d399; border:1px solid #1e3a29; border-radius:20px;">✦ Harapan Kami</span>
            <p style="font-size:12px; color:#8fac98; line-height:1.75; font-style:italic; text-align:center; margin:0;">
                "Save Bite hadir untuk membantu makanan surplus menemukan orang yang membutuhkannya. Bersama-sama, kita memperpanjang umur makanan, mengurangi sampah, menekan jejak emisi karbon, mendukung usaha lokal, dan membangun kebiasaan konsumsi yang lebih bijak demi masa depan bumi yang lebih sehat."
            </p>
            <p style="font-size:11px; color:#34d399; line-height:1.5; text-align:center; margin:14px 0 0; font-weight:800;">
                Gielang Omar Khadavi <span style="color:#4b6353; font-weight:600;">· CEO of SaveBite</span>
            </p>
        </div>

        <!-- SOCIAL LINKS -->
        <div>
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                <hr style="flex:1; height:1px; background:#1a2b1f; border:none;">
                <span style="font-size:9px; font-weight:800; letter-spacing:1.8px; text-transform:uppercase; color:#34d399; white-space:nowrap;">Ikuti Perjalanan Kami</span>
                <hr style="flex:1; height:1px; background:#1a2b1f; border:none;">
            </div>

            <p style="max-width:42rem; margin:0 auto 14px; font-size:12px; color:#8fac98; line-height:1.7; text-align:center;">
                Dapatkan kabar terbaru tentang penyelamatan makanan, cerita merchant, dan gerakan kecil yang berdampak besar bagi lingkungan.
            </p>
            <div style="display:flex; justify-content:center; flex-wrap:wrap; gap:8px;">
                <a href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer" aria-label="Instagram Save Bite" style="display:inline-flex; align-items:center; gap:7px; color:#d1fae5; background:#111f17; border:1px solid #1a2b1f; border-radius:10px; padding:9px 14px; font-size:11px; font-weight:700; text-decoration:none;">
                    <span aria-hidden="true">◎</span> Instagram
                </a>
                <a href="https://www.tiktok.com/" target="_blank" rel="noopener noreferrer" aria-label="TikTok Save Bite" style="display:inline-flex; align-items:center; gap:7px; color:#d1fae5; background:#111f17; border:1px solid #1a2b1f; border-radius:10px; padding:9px 14px; font-size:11px; font-weight:700; text-decoration:none;">
                    <span aria-hidden="true">♪</span> TikTok
                </a>
                <a href="https://www.linkedin.com/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn Save Bite" style="display:inline-flex; align-items:center; gap:7px; color:#d1fae5; background:#111f17; border:1px solid #1a2b1f; border-radius:10px; padding:9px 14px; font-size:11px; font-weight:700; text-decoration:none;">
                    <span aria-hidden="true">in</span> LinkedIn
                </a>
            </div>
        </div>

        <hr style="border:none; height:1px; background:#1a2b1f; margin:0;">

        <!-- COPYRIGHT -->
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:4px;">
            <span style="font-size:10px; color:#2d4535;">© 2026 Save Bite Movement</span>
            <span style="font-size:10px; color:#2d4535;">🌱 Made with care for the planet</span>
        </div>

    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ============== SEARCH FUNCTIONALITY ==============
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            const productCards = document.querySelectorAll('.product-card');
            const emptyState = document.getElementById('searchEmptyState');

            searchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();
                let visibleCount = 0;

                productCards.forEach((card) => {
                    const searchableText = (card.dataset.search || '').toLowerCase();
                    const matches = !query || searchableText.includes(query);
                    card.style.display = matches ? '' : 'none';
                    if (matches) visibleCount += 1;
                });

                if (emptyState) {
                    emptyState.classList.toggle('hidden', visibleCount > 0 || productCards.length === 0);
                }
            });
        }

        // ============== GPS GEOLOCATION ==============
        const gpsButton = document.getElementById('gpsButton');
        const gpsIcon = document.getElementById('gpsIcon');
        const gpsText = document.getElementById('gpsText');

        if (gpsButton) {
            function requestLocation() {
                if ('geolocation' in navigator) {
                    gpsIcon.textContent = '⏳';
                    gpsText.textContent = 'Meminta lokasi...';
                    gpsButton.disabled = true;

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lon = position.coords.longitude;

                            // Simpan ke session storage
                            sessionStorage.setItem('user_latitude', lat);
                            sessionStorage.setItem('user_longitude', lon);

                            // Reload halaman dengan query string untuk sort produk
                            window.location.href = `?lat=${lat}&lon=${lon}`;
                        },
                        (error) => {
                            gpsIcon.textContent = '❌';
                            gpsText.textContent = 'Lokasi ditolak';
                            gpsButton.disabled = false;

                            setTimeout(() => {
                                gpsIcon.textContent = '📍';
                                gpsText.textContent = 'Lokasi Saya';
                            }, 2000);

                            console.error('Geolocation error:', error);
                        }
                    );
                } else {
                    alert('Browser Anda tidak mendukung Geolocation API');
                }
            }

            gpsButton.addEventListener('click', requestLocation);

            // Pelanggan yang baru login langsung diminta mengizinkan lokasi agar
            // daftar rekomendasi bintang 5 bisa disusun berdasarkan jarak.
            @auth
            const locationParams = new URLSearchParams(window.location.search);
            if (!locationParams.has('lat') || !locationParams.has('lon')) {
                requestLocation();
            }
            @endauth
        }
    });
</script>

</body>
</html>
