<x-guest-layout>
    <div class="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl sm:p-10">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-700 text-2xl text-white">✉</div>
            <h1 class="text-2xl font-black text-slate-900">Verifikasi OTP</h1>
            <p class="mt-2 text-sm text-slate-500">Masukkan kode 6 digit yang kami kirim ke email kamu. Kode berlaku selama 10 menit.</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('otp.store') }}" class="space-y-5">
            @csrf
            <div>
                <label for="code" class="mb-2 block text-sm font-bold text-slate-700">Kode OTP</label>
                <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" maxlength="6" required autofocus
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-center text-2xl font-bold tracking-[0.45em] text-slate-900 outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20" />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>

            <button type="submit" class="w-full rounded-xl bg-emerald-700 px-4 py-3 font-bold text-white transition hover:bg-emerald-800">Verifikasi & Masuk</button>
        </form>

        <form method="POST" action="{{ route('otp.resend') }}" class="mt-4 text-center">
            @csrf
            <button type="submit" class="text-sm font-bold text-emerald-700 hover:text-emerald-800">Kirim ulang OTP</button>
        </form>
    </div>
</x-guest-layout>
