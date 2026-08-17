<x-guest-layout>
    <!-- Main Card Container -->
    <div class="max-w-5xl w-full bg-white rounded-[32px] border border-slate-900/10 shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2 relative min-h-[580px]">
        
        <!-- LEFT COLUMN: Form Section -->
        <div class="p-8 sm:p-10 md:p-12 flex flex-col justify-between relative z-10 bg-white">
            
            <!-- Brand Logo -->
            <div>
                <div class="flex items-center gap-2.5 mb-8">
                    <div class="w-8 h-8 rounded-lg bg-emerald-800 flex items-center justify-center text-white font-black text-sm shadow-sm">
                        🌱
                    </div>
                    <span class="text-xl font-black text-slate-900 tracking-tight">SurplusBite</span>
                </div>

                <!-- Title Header -->
                <div class="space-y-1 mb-8">
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Welcome Back!</h1>
                    <p class="text-sm font-medium text-slate-500">Please Log in to your account.</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ request()->query('redirect', route('dashboard', absolute: false)) }}">

                    <!-- Email Input -->
                    <div class="space-y-1">
                        <label for="email" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Email Address</label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email', 'tuheirana@gmail.com') }}" 
                               required 
                               autofocus 
                               autocomplete="username"
                               placeholder="nama@email.com"
                               class="w-full rounded-xl border border-emerald-800/30 bg-white px-4 py-3 text-sm font-medium text-slate-800 placeholder-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 transition outline-none" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-red-500" />
                    </div>

                    <!-- Password Input -->
                    <div class="space-y-1">
                        <label for="password" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">Password</label>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-800 placeholder-slate-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 transition outline-none" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-red-500" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer text-slate-600 font-medium select-none">
                            <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-emerald-800 shadow-sm focus:ring-emerald-600 cursor-pointer" name="remember">
                            <span class="ml-2">Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="font-semibold text-rose-500 hover:text-rose-600 transition" href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <!-- Buttons Row: Login & Create Account -->
                    <div class="flex items-center gap-3 pt-3">
                        <button type="submit" 
                                class="px-8 py-3 rounded-xl bg-[#0d5c58] hover:bg-[#094643] text-white font-bold text-sm shadow-md shadow-emerald-900/10 transition-all duration-200 active:scale-95">
                            Login
                        </button>

                        <a href="{{ route('register') }}" 
                           class="px-6 py-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm transition-all duration-200 active:scale-95 text-center">
                            Create account
                        </a>
                    </div>
                </form>
            </div>

            <!-- Policy Footer -->
            <div class="pt-8">
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    By sign up you agree to our <a href="#" class="underline text-slate-500 hover:text-slate-800">terms</a> and that you have read our <a href="#" class="underline text-slate-500 hover:text-slate-800">data policy</a>.
                </p>
            </div>
        </div>

        <!-- FLOATING YELLOW CIRCLE BUTTON (Center Divider) -->
        <div class="hidden md:flex absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 z-30">
            <button type="button" class="w-10 h-10 rounded-full bg-amber-400 hover:bg-amber-500 text-white shadow-lg flex items-center justify-center transition-transform hover:scale-110 active:scale-95">
                <svg class="w-4 h-4 fill-current ml-0.5 text-white" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
            </button>
        </div>

        <!-- RIGHT COLUMN: Nature & Bird Visual Banner -->
        <div class="relative hidden md:block bg-emerald-950 overflow-hidden">
            <!-- Background Image -->
            <img src="https://images.unsplash.com/photo-1542273917363-3b1817f69a2d?auto=format&fit=crop&w=1200&q=80" 
                 alt="Nature Eco SurplusBite" 
                 class="w-full h-full object-cover opacity-90 transition-transform duration-700 hover:scale-105">

            <!-- Dark Vignette Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/70 via-transparent to-black/20"></div>

            <!-- Glass Overlay Pattern (Leaf / Geometric graphic effect) -->
            <div class="absolute inset-0 pointer-events-none flex items-center justify-center opacity-30">
                <svg class="w-3/4 h-3/4 text-white" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="0.8">
                    <path d="M50 10 C 20 40, 20 70, 50 90 C 80 70, 80 40, 50 10 Z" />
                    <path d="M50 10 L 50 90" />
                    <path d="M30 40 Q 50 50 70 40" />
                    <path d="M25 60 Q 50 70 75 60" />
                </svg>
            </div>

            <!-- Bottom Right Slider Control -->
            <div class="absolute bottom-6 right-6 flex items-center gap-2 bg-white/90 backdrop-blur-md px-3 py-1.5 rounded-lg shadow-lg text-slate-800">
                <button type="button" class="text-slate-400 hover:text-slate-900 font-bold text-xs px-1">&larr;</button>
                <span class="text-xs font-bold px-1">1</span>
                <button type="button" class="text-slate-400 hover:text-slate-900 font-bold text-xs px-1">&rarr;</button>
            </div>
        </div>

    </div>
</x-guest-layout>