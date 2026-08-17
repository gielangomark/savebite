<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SurplusBite - Save Food, Save Planet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Keyframe Animations */
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
        @keyframes orbit {
            from { transform: rotate(0deg) translateX(80px) rotate(0deg); }
            to { transform: rotate(360deg) translateX(80px) rotate(-360deg); }
        }

        .animate-float { animation: float 4s ease-in-out infinite; }
        .animate-float-reverse { animation: floatReverse 5s ease-in-out infinite; }
        .animate-spin-slow { animation: spinSlow 25s linear infinite; }
        .animate-glow { animation: pulseGlow 3s ease-in-out infinite; }
        .animate-orbit { animation: orbit 8s linear infinite; }

        .glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .glass-dark {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans" 
      x-data="{ 
          showIntro: true, 
          progress: 0, 
          loadingText: '🔍 Menyiapkan porsi makanan surplus...' 
      }" 
      x-init="
          let timer = setInterval(() => {
              if (progress < 100) {
                  progress += 2;
                  if (progress === 30) loadingText = '🌱 Menghitung potensi penekanan emisi CO2...';
                  if (progress === 60) loadingText = '⚡ Membaca data merchant & kantin terdekat...';
                  if (progress === 90) loadingText = '✨ Siap! Selamatkan makanan & bumi sekarang.';
              } else {
                  clearInterval(timer);
                  setTimeout(() => { showIntro = false; }, 400);
              }
          }, 100);
      ">

    <!-- ========================================== -->
    <!-- 🌍 FULLSCREEN ECO INTRO / SPLASH SCREEN 5S -->
    <!-- ========================================== -->
    <div x-show="showIntro" 
         x-transition:leave="transition ease-in duration-700 transform"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-105"
         class="fixed inset-0 z-[100] bg-gradient-to-br from-slate-950 via-emerald-950 to-teal-950 text-white flex flex-col justify-between items-center p-6 overflow-hidden">
        
        <!-- Ambient Glowing Background Elements -->
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl animate-glow pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-teal-500/20 rounded-full blur-3xl animate-glow pointer-events-none"></div>

        <!-- Floating Eco Elements -->
        <div class="absolute top-12 left-10 text-3xl animate-float opacity-40 pointer-events-none">🍃</div>
        <div class="absolute bottom-20 left-12 text-3xl animate-float-reverse opacity-40 pointer-events-none">🌾</div>
        <div class="absolute top-20 right-14 text-2xl animate-float opacity-30 pointer-events-none">✨</div>
        <div class="absolute bottom-28 right-12 text-3xl animate-float-reverse opacity-40 pointer-events-none">♻️</div>

        <!-- Top Header & Skip Button -->
        <div class="w-full max-w-2xl flex justify-between items-center relative z-10 pt-2">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full glass-dark border border-emerald-500/30 text-emerald-300 text-xs font-bold tracking-wider uppercase">
                <span>🌱 SURPLUSBITE ECO MOVEMENT</span>
            </div>
            <button @click="showIntro = false" 
                    class="text-xs font-semibold text-slate-300 hover:text-white bg-white/10 hover:bg-white/20 px-4 py-2 rounded-full border border-white/10 backdrop-blur-md transition flex items-center gap-1">
                <span>Lewati</span>
                <span>➔</span>
            </button>
        </div>

        <!-- Middle Core Content: Animated Earth Globe & Quote -->
        <div class="flex flex-col items-center text-center max-w-2xl mx-auto my-auto relative z-10 space-y-6">
            
            <!-- Animated 3D Glowing Earth Globe -->
            <div class="relative flex items-center justify-center my-2">
                <!-- Atmosphere Aura Glow -->
                <div class="absolute w-44 h-44 rounded-full bg-emerald-500/30 blur-2xl animate-pulse"></div>
                <div class="absolute w-40 h-40 rounded-full border-2 border-dashed border-emerald-400/40 animate-spin-slow"></div>
                
                <!-- Earth Planet Icon -->
                <div class="relative w-36 h-36 rounded-full bg-gradient-to-tr from-teal-600 via-emerald-500 to-lime-400 flex items-center justify-center shadow-[0_0_50px_rgba(16,185,129,0.5)] border-2 border-white/30 transform hover:scale-105 transition duration-500">
                    <svg class="w-24 h-24 text-slate-900/80 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a2.5 2.5 0 002.5-2.5V7.865M12 21a9 9 0 100-18 9 9 0 000 18z"></path>
                    </svg>
                </div>

                <!-- Orbiting Leaf Particle -->
                <div class="absolute animate-orbit pointer-events-none">
                    <span class="text-xl">🍃</span>
                </div>
            </div>

            <!-- Pesan Utama Lingkungan -->
            <div class="space-y-3 px-2">
                <h1 class="text-2xl md:text-4xl font-black leading-tight tracking-tight">
                    "Jagalah lingkunganmu dengan tidak membuang makanan tak terjual."
                </h1>
                
                <!-- Slogan Badge -->
                <div class="inline-block pt-1">
                    <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-lime-300 bg-clip-text text-transparent text-sm md:text-lg font-extrabold uppercase tracking-widest drop-shadow-sm">
                        ⚡ Bangkitkan Ekonomi & Kurangi Limbah ⚡
                    </span>
                </div>
            </div>
        </div>

        <!-- Bottom Loading & Progress Bar (5 Sec) -->
        <div class="w-full max-w-md mx-auto relative z-10 pb-4 space-y-2">
            <div class="flex justify-between items-center text-xs font-medium text-emerald-200/90">
                <span x-text="loadingText" class="truncate max-w-[80%]"></span>
                <span x-text="progress + '%'" class="font-bold text-lime-300 font-mono"></span>
            </div>

            <!-- Progress Bar Outer -->
            <div class="w-full bg-slate-800/80 rounded-full h-3 p-0.5 border border-white/10 shadow-inner overflow-hidden backdrop-blur-md">
                <!-- Progress Bar Inner Glowing -->
                <div class="bg-gradient-to-r from-emerald-500 via-teal-400 to-lime-400 h-full rounded-full transition-all duration-100 ease-linear shadow-[0_0_12px_rgba(52,211,153,0.8)]"
                     :style="'width: ' + progress + '%'"></div>
            </div>
        </div>
    </div>


    <!-- ========================================== -->
    <!-- MAIN NAVBAR & KATALOG UTAMA SURPLUSBITE    -->
    <!-- ========================================== -->
    <nav class="sticky top-0 z-50 glass border-b border-emerald-100 shadow-sm transition-all duration-300">
        <div class="max-w-5xl mx-auto px-4 py-3 flex justify-between items-center">
            
            <!-- Logo SurplusBite -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white shadow-lg shadow-emerald-600/30 group-hover:scale-105 transition-transform">
                    🌱
                </div>
                <div>
                    <span class="text-xl font-black bg-gradient-to-r from-emerald-800 via-teal-700 to-emerald-600 bg-clip-text text-transparent">SurplusBite</span>
                    <span class="block text-[10px] text-emerald-600 font-semibold tracking-wider uppercase">Eco-Campus Movement</span>
                </div>
            </a>

            <!-- Menu Kanan: Login / Register ATAU Profile User -->
            <div class="flex items-center gap-3">
                
                {{-- KALAU USER BELUM LOGIN (GUEST) --}}
                @guest
                    <a href="{{ route('login', ['redirect' => url()->current()]) }}" 
                       class="text-xs font-bold text-slate-700 hover:text-emerald-700 px-3.5 py-2 rounded-xl border border-slate-200 hover:border-emerald-300 transition bg-white/80 shadow-sm">
                        Masuk
                    </a>
                    <a href="{{ route('register', ['redirect' => url()->current()]) }}" 
                       class="text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 px-4 py-2 rounded-xl shadow-md shadow-emerald-600/20 transition transform active:scale-95">
                        Daftar
                    </a>
                @endguest

                {{-- KALAU USER SUDAH LOGIN (AUTH) --}}
                @auth
                    {{-- Tombol Khusus Penjual --}}
                    @if(Auth::user()->isMerchant())
                        <a href="{{ route('merchant.dashboard') }}" 
                           class="hidden sm:flex items-center gap-1.5 text-xs font-bold text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 px-3 py-2 rounded-xl transition shadow-sm">
                            <span>🏪 Dashboard Penjual</span>
                        </a>
                    @endif

                    <!-- User Profile Dropdown Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="flex items-center gap-2 text-xs font-bold text-slate-800 bg-white hover:bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200 transition shadow-sm">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-[10px] font-black">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <span class="max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Popover Dropdown -->
                        <div x-show="open" 
                             @click.outside="open = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            
                            <!-- Header Info User -->
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-[10px] text-slate-400 font-medium">Masuk sebagai</p>
                                <p class="text-xs font-extrabold text-slate-800 truncate">{{ Auth::user()->email }}</p>
                                <span class="inline-block mt-1 text-[9px] font-black uppercase px-2 py-0.5 rounded-md {{ Auth::user()->isMerchant() ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ Auth::user()->isMerchant() ? '🏪 Penjual (Mitra)' : '🎓 Mahasiswa' }}
                                </span>
                            </div>

                            <!-- Menu Khusus Mobile jika Penjual -->
                            @if(Auth::user()->isMerchant())
                                <a href="{{ route('merchant.dashboard') }}" class="sm:hidden block px-4 py-2 text-xs font-bold text-amber-800 hover:bg-amber-50">
                                    🏪 Dashboard Penjual
                                </a>
                            @endif

                            <!-- Form Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 transition flex items-center gap-2 mt-1">
                                    <span>🚪 Keluar (Logout)</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth

            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-emerald-900 via-teal-900 to-slate-900 text-white pt-10 pb-16 px-4">
        <div class="absolute top-6 left-8 text-3xl animate-float opacity-30 pointer-events-none">🍃</div>
        <div class="absolute bottom-6 right-10 text-4xl animate-float-reverse opacity-30 pointer-events-none">🌿</div>
        <div class="absolute top-1/2 right-1/4 text-2xl animate-float opacity-20 pointer-events-none">✨</div>
        <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-emerald-500/20 rounded-full blur-3xl animate-glow pointer-events-none"></div>

        <div class="max-w-4xl mx-auto text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-medium mb-4 backdrop-blur-md">
                <span>🌎 Zero Food Waste Campus Mission</span>
            </div>
            <h1 class="text-3xl md:text-5xl font-extrabold leading-tight tracking-tight">
                Selamatkan Makanan Lezat,<br>
                <span class="bg-gradient-to-r from-emerald-300 via-teal-200 to-lime-300 bg-clip-text text-transparent">
                    Potong Emisi Karbon Kampus!
                </span>
            </h1>
            <p class="text-emerald-100/80 text-sm md:text-base mt-3 max-w-2xl mx-auto leading-relaxed">
                Dapatkan makanan kualitas prima dari kantin & resto sekitar dengan diskon hingga 70% sebelum jam operasional berakhir.
            </p>

            <!-- Dynamic Impact Counter Card -->
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

    <!-- Wave Separator Decorative -->
    <div class="w-full overflow-hidden leading-none -mt-1">
        <svg class="relative block w-full h-10 text-slate-50" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="currentColor"></path>
        </svg>
    </div>

    <!-- Main Content Katalog -->
    <main class="max-w-5xl mx-auto px-4 py-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    🍲 Pilihan Makanan Hari Ini
                </h2>
                <p class="text-xs text-slate-500">Stok terbatas! Segera booking sebelum kehabisan jam ambil.</p>
            </div>
        </div>

        <!-- Grid Cards Makanan -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
            @foreach($products as $product)
            <div class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-2xl hover:border-emerald-200 transition-all duration-500 overflow-hidden flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <!-- Image Wrapper -->
                    <div class="relative overflow-hidden h-48">
                        <img src="{{ $product->image_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                        
                        <!-- Badges -->
                        <div class="absolute top-3 left-3 flex gap-2">
                            <span class="glass text-emerald-900 text-[11px] font-extrabold px-3 py-1 rounded-full border border-white/40 shadow-sm flex items-center gap-1">
                                🏢 {{ $product->merchant->name }}
                            </span>
                        </div>

                        <div class="absolute top-3 right-3">
                            <span class="bg-gradient-to-r from-red-600 to-amber-500 text-white text-xs font-black px-3 py-1 rounded-full shadow-lg animate-pulse">
                                Save {{ round((($product->original_price - $product->discount_price) / $product->original_price) * 100) }}%
                            </span>
                        </div>

                        <div class="absolute bottom-3 left-3 right-3 text-white">
                            <h3 class="font-black text-lg text-white group-hover:text-emerald-300 transition-colors drop-shadow-md">
                                {{ $product->title }}
                            </h3>
                        </div>
                    </div>

                    <!-- Details Content -->
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <span class="flex items-center gap-1 font-medium">📍 {{ $product->merchant->location ?? 'Lokasi' }}</span>
                            <span class="font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/60">
                                ⏰ {{ $product->pickup_time }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 text-[11px] text-emerald-700 font-medium">
                            <span>🌱 Estimasi cegah 0.8 kg CO2</span>
                            <span>•</span>
                            <span class="text-slate-500">Sisa {{ $product->stock }} porsi</span>
                        </div>
                    </div>
                </div>

                <!-- Card Footer & Action Button -->
                <div class="p-5 pt-0 flex items-center justify-between border-t border-slate-100 mt-2">
                    <div>
                        <span class="text-xs text-slate-400 line-through font-medium">Rp {{ number_format($product->original_price) }}</span>
                        <div class="text-xl font-black text-emerald-700 tracking-tight">
                            Rp {{ number_format($product->discount_price) }}
                        </div>
                    </div>
                    <a href="{{ route('food.detail', $product->id) }}" 
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs px-5 py-3 rounded-2xl shadow-lg shadow-emerald-600/20 hover:shadow-emerald-600/40 transition-all duration-300 transform active:scale-95">
                        <span>Detail & Save</span>
                        <span>➔</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </main>

    <!-- Footer Simple -->
    <footer class="mt-16 bg-slate-900 text-slate-400 py-8 px-4 text-center text-xs border-t border-slate-800">
        <p class="font-medium text-slate-300 mb-1">🌱 SurplusBite Innovation — Lomba Creative Idea Competition</p>
        <p class="text-slate-500">Mendorong Gaya Hidup Ramah Lingkungan & Zero Food Waste di Lingkungan Kampus.</p>
    </footer>

</body>
</html>