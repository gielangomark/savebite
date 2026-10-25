<section class="space-y-5">
    <header>
        <h2 class="text-[15px] font-black text-slate-900">Ubah Kata Sandi</h2>
        <p class="mt-1.5 text-[13px] leading-relaxed text-slate-500">
            Gunakan kata sandi yang panjang dan acak agar akunmu tetap aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        {{-- Current password --}}
        <div>
            <label for="update_password_current_password" class="block text-[12px] font-bold text-slate-700">
                Kata sandi saat ini
            </label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 outline-none transition
                       focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
            >
            @foreach ($errors->updatePassword->get('current_password') as $message)
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @endforeach
        </div>

        {{-- New password --}}
        <div>
            <label for="update_password_password" class="block text-[12px] font-bold text-slate-700">
                Kata sandi baru
            </label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                autocomplete="new-password"
                class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 outline-none transition
                       focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
            >
            @foreach ($errors->updatePassword->get('password') as $message)
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @endforeach
        </div>

        {{-- Confirm password --}}
        <div>
            <label for="update_password_password_confirmation" class="block text-[12px] font-bold text-slate-700">
                Konfirmasi kata sandi
            </label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 outline-none transition
                       focus:border-emerald-500 focus:bg-white focus:ring-2 focus:ring-emerald-500/20"
            >
            @foreach ($errors->updatePassword->get('password_confirmation') as $message)
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @endforeach
        </div>

        {{-- CTA --}}
        <div class="flex items-center gap-4 border-t border-slate-100 pt-5">
            <button
                type="submit"
                class="rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 px-5 py-2.5 text-sm font-black text-white shadow-lg shadow-emerald-700/20 transition hover:opacity-90 active:scale-[.98]"
            >
                Simpan kata sandi
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-bold text-emerald-600"
                >
                    ✓ Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>