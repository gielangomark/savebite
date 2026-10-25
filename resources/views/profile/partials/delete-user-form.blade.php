<section class="space-y-5">
    <header>
        <h2 class="text-[15px] font-black text-rose-700">Hapus Akun</h2>
        <p class="mt-1.5 text-[13px] leading-relaxed text-rose-500/80">
            Setelah akun dihapus, semua data akan hilang permanen. Unduh data pentingmu sebelum melanjutkan.
        </p>
    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="rounded-xl border border-rose-200 bg-white px-4 py-2.5 text-sm font-bold text-rose-600 transition hover:border-rose-300 hover:bg-rose-50 active:scale-[.98]"
    >
        Hapus akun saya
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-7">
            @csrf
            @method('delete')

            {{-- Modal header --}}
            <div class="mb-6 flex items-start gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-rose-100 text-xl">
                    🗑️
                </div>
                <div>
                    <h2 class="text-[16px] font-black text-slate-900">Hapus akun secara permanen?</h2>
                    <p class="mt-1 text-[13px] leading-relaxed text-slate-500">
                        Semua data akan dihapus dan tidak bisa dipulihkan. Masukkan kata sandi untuk konfirmasi.
                    </p>
                </div>
            </div>

            {{-- Password field --}}
            <div>
                <label for="password" class="sr-only">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Kata sandi kamu"
                    class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 outline-none transition
                           focus:border-rose-400 focus:bg-white focus:ring-2 focus:ring-rose-400/20"
                >
                @foreach ($errors->userDeletion->get('password') as $message)
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex items-center justify-end gap-3">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50 active:scale-[.98]"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-black text-white shadow-lg shadow-rose-600/20 transition hover:bg-rose-700 active:scale-[.98]"
                >
                    Ya, hapus akun
                </button>
            </div>
        </form>
    </x-modal>
</section>