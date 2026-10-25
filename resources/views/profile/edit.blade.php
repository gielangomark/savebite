@php
    $avatarPresets = [
        'leaf'      => ['emoji' => '🍃', 'label' => 'Daun Ceria',  'class' => 'from-emerald-400 to-teal-600',  'gradient' => 'linear-gradient(135deg,#4ade80,#16a34a)'],
        'apple'     => ['emoji' => '🍎', 'label' => 'Apel Ramah',  'class' => 'from-rose-400 to-orange-500',   'gradient' => 'linear-gradient(135deg,#fb7185,#f97316)'],
        'tree'      => ['emoji' => '🌳', 'label' => 'Pohon Teduh', 'class' => 'from-lime-400 to-emerald-700',  'gradient' => 'linear-gradient(135deg,#a3e635,#15803d)'],
        'sunflower' => ['emoji' => '🌻', 'label' => 'Bunga Cerah', 'class' => 'from-amber-300 to-orange-500',  'gradient' => 'linear-gradient(135deg,#fde047,#f97316)'],
    ];
    $currentPreset = str_starts_with((string) $user->avatar, 'preset:') ? substr($user->avatar, 7) : null;
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil – Save Bite</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f9fdf9] antialiased">

<div class="px-4 py-8 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">

        {{-- TOP ROW --}}
        <div class="grid gap-5 lg:grid-cols-[320px_1fr]">

            {{-- SIDEBAR / IDENTITY CARD --}}
            <aside class="relative overflow-hidden rounded-[28px] bg-[#0a1a0f] p-7 text-white shadow-2xl shadow-emerald-950/20">
                {{-- Glow --}}
                <div class="pointer-events-none absolute -right-14 -top-14 h-56 w-56 rounded-full bg-emerald-500/10 blur-3xl"></div>

                {{-- Eyebrow --}}
                <div class="mb-5 inline-flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-emerald-400">
                    🌱 Save Bite identity
                </div>

                <h1 class="text-[26px] font-black leading-tight">
                    Tumbuhkan<br><span class="text-emerald-400">versi terbaikmu.</span>
                </h1>

                {{-- Avatar preview --}}
                <div class="my-7 flex flex-col items-center gap-3 text-center">
                    <div
                        id="avatar-preview"
                        class="flex h-24 w-24 items-center justify-center rounded-[22px] text-5xl shadow-2xl ring-[3px] ring-[#0a1a0f] ring-offset-[3px] ring-offset-emerald-500/40
                               {{ $currentPreset && isset($avatarPresets[$currentPreset]) ? 'bg-gradient-to-br '.$avatarPresets[$currentPreset]['class'] : 'bg-gradient-to-br from-emerald-400 to-teal-600' }}"
                    >
                        @if ($currentPreset && isset($avatarPresets[$currentPreset]))
                            {{ $avatarPresets[$currentPreset]['emoji'] }}
                        @elseif ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Foto profil" class="h-full w-full rounded-[20px] object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>

                    <div>
                        <p class="text-[17px] font-bold text-slate-100">{{ $user->name }}</p>
                        <p class="mt-0.5 text-sm text-slate-500">{{ $user->email }}</p>
                    </div>

                    <div class="mt-1 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-4 py-1.5 text-[11px] font-semibold text-emerald-400">
                        🌍 Penjaga bumi setiap hari
                    </div>
                </div>

                {{-- Join year --}}
                <div class="border-t border-white/5 pt-5 text-center">
                    <p class="text-3xl font-black text-emerald-400">{{ $user->created_at?->format('Y') }}</p>
                    <p class="mt-1 text-[10px] uppercase tracking-widest text-slate-600">Tahun bergabung di Save Bite</p>
                </div>
            </aside>

            {{-- MAIN FORM CARD --}}
            <section class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-xl shadow-slate-200/60">
                {{-- Header --}}
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-7 py-6">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[.13em] text-emerald-600">Personalization</p>
                        <h2 class="mt-1 text-xl font-black text-slate-900">Atur profilmu</h2>
                        <p class="mt-1.5 text-[13px] text-slate-500">Pilih wajah kecil yang paling menggambarkan semangatmu.</p>
                    </div>
                    <span class="hidden rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-[10px] font-bold text-emerald-700 sm:block">
                        Tersimpan aman
                    </span>
                </div>

                <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="px-7 py-6">
                    @csrf
                    @method('patch')

                    {{-- Avatar label --}}
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-[12px] font-bold text-slate-700">Avatar profil</span>
                        <span class="text-[11px] text-slate-400">Pilih salah satu</span>
                    </div>

                    {{-- Preset grid --}}
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach ($avatarPresets as $key => $preset)
                            <label class="group cursor-pointer">
                                <input
                                    type="radio"
                                    name="avatar_preset"
                                    value="{{ $key }}"
                                    data-emoji="{{ $preset['emoji'] }}"
                                    data-gradient="{{ $preset['gradient'] }}"
                                    class="peer sr-only"
                                    {{ $currentPreset === $key ? 'checked' : '' }}
                                >
                                <span class="flex flex-col items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-3 text-center transition-all
                                             group-hover:border-emerald-300 group-hover:bg-emerald-50/50
                                             peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:shadow-[0_0_0_3px_#16a34a18]">
                                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br {{ $preset['class'] }} text-3xl shadow-sm">
                                        {{ $preset['emoji'] }}
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-600">{{ $preset['label'] }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>

                    {{-- Upload row --}}
                    <label class="mt-3 flex cursor-pointer items-center gap-3 rounded-2xl border border-dashed border-slate-300 px-4 py-3 transition hover:border-emerald-400 hover:bg-emerald-50/50">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-lg">📷</span>
                        <span class="flex-1">
                            <span class="block text-[13px] font-bold text-slate-700">Upload foto sendiri</span>
                            <span class="block text-[11px] text-slate-400">JPG, PNG atau WEBP, maksimal 4 MB</span>
                        </span>
                        <input id="avatar_photo" name="avatar_photo" type="file" accept="image/*" class="sr-only">
                    </label>

                    @error('avatar_photo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('avatar_preset')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- Divider --}}
                    <div class="my-6 h-px bg-slate-100"></div>

                    {{-- Name + Phone --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-[12px] font-bold text-slate-700">Nama lengkap</label>
                            <input
                                id="name" name="name" type="text" required autocomplete="name"
                                value="{{ old('name', $user->name) }}"
                                class="mt-2 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                            >
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-[12px] font-bold text-slate-700">Nomor WhatsApp</label>
                            <input
                                id="phone" name="phone" type="tel" autocomplete="tel" placeholder="081234567890"
                                value="{{ old('phone', $user->phone) }}"
                                class="mt-2 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                            >
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="mt-4">
                        <label for="email" class="block text-[12px] font-bold text-slate-700">Alamat email</label>
                        <input
                            id="email" name="email" type="email" required autocomplete="username"
                            value="{{ old('email', $user->email) }}"
                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- CTA --}}
                    <div class="mt-6 flex flex-wrap items-center gap-4 border-t border-slate-100 pt-6">
                        <button
                            type="submit"
                            class="rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 px-5 py-2.5 text-sm font-black text-white shadow-lg shadow-emerald-700/20 transition hover:opacity-90 active:scale-[.98]"
                        >
                            Simpan perubahan
                        </button>
                        @if (session('status') === 'profile-updated')
                            <span class="text-sm font-bold text-emerald-600">✓ Perubahan tersimpan.</span>
                        @endif
                    </div>
                </form>
            </section>
        </div>

        {{-- BOTTOM ROW --}}
        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            {{-- Password --}}
            <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white p-7 shadow-lg shadow-slate-200/50">
                @include('profile.partials.update-password-form')
            </div>

            {{-- Delete --}}
            <div class="overflow-hidden rounded-[28px] border border-rose-100 bg-rose-50/60 p-7 shadow-lg shadow-rose-100/30">
                @include('profile.partials.delete-user-form')
            </div>
        </div>

    </div>
</div>

<script>
    const avatarPhoto   = document.getElementById('avatar_photo');
    const avatarPreview = document.getElementById('avatar-preview');

    avatarPhoto?.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = () => {
            avatarPreview.innerHTML = `<img src="${reader.result}" alt="Preview foto profil" class="h-full w-full rounded-[20px] object-cover">`;
            avatarPreview.className = 'flex h-24 w-24 items-center justify-center rounded-[22px] bg-slate-200 text-5xl shadow-2xl ring-[3px] ring-[#0a1a0f] ring-offset-[3px] ring-offset-emerald-500/40';
        };
        reader.readAsDataURL(file);
    });

    document.querySelectorAll('input[name="avatar_preset"]').forEach((input) => {
        input.addEventListener('change', () => {
            avatarPreview.textContent = input.dataset.emoji;
            avatarPreview.style.background = input.dataset.gradient;
            avatarPreview.className = 'flex h-24 w-24 items-center justify-center rounded-[22px] text-5xl shadow-2xl ring-[3px] ring-[#0a1a0f] ring-offset-[3px] ring-offset-emerald-500/40';
        });
    });
</script>

</body>
</html>