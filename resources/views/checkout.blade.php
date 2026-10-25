<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Checkout - Save Bite</title>
</head>
<body class="min-h-screen bg-[#f3f7f5] text-slate-900 antialiased">
    @php($walletBalance = (int) (Auth::user()->wallet?->balance ?? 0))
    @php($canUseWallet = $walletBalance >= $totalPrice)

    <div class="min-h-screen bg-[radial-gradient(circle_at_top_right,_rgba(16,185,129,0.14),_transparent_36rem)] px-4 py-5 sm:px-6 sm:py-8">
        <header class="mx-auto flex max-w-5xl items-center justify-between">
            <a href="{{ route('food.detail', $product) }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-emerald-700">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-lg shadow-sm ring-1 ring-slate-200">&larr;</span>
                <span>Kembali ke menu</span>
            </a>
            <span class="hidden text-xs font-bold uppercase tracking-[0.18em] text-emerald-700 sm:block">Checkout aman</span>
        </header>

        <main class="mx-auto mt-8 max-w-5xl">
            <div class="mb-7">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-emerald-700">Satu langkah lagi</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Selesaikan pesananmu</h1>
                <p class="mt-2 text-sm leading-6 text-slate-500">Pilih cara pembayaran, lalu selesaikan pesanan dalam satu langkah.</p>
            </div>

            <div class="grid gap-5 lg:grid-cols-[1.15fr_0.85fr]">
                <section class="overflow-hidden rounded-3xl bg-white shadow-[0_18px_50px_rgba(15,23,42,0.08)] ring-1 ring-slate-200/70">
                    <div class="relative h-52 overflow-hidden bg-emerald-950 sm:h-64">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center text-6xl">&#127869;</div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-transparent to-transparent"></div>
                        <div class="absolute bottom-5 left-5 text-white">
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-emerald-200">{{ $product->merchant->name }}</p>
                            <h2 class="mt-1 text-2xl font-black sm:text-3xl">{{ $product->title }}</h2>
                        </div>
                    </div>
                    <div class="space-y-5 p-5 sm:p-7">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-emerald-50 p-4"><p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Waktu ambil</p><p class="mt-1 font-black">{{ $product->pickup_time }} - {{ $product->pickup_end_time }} WIB</p></div>
                            <div class="rounded-2xl bg-slate-100 p-4"><p class="text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah</p><p class="mt-1 font-black">{{ $quantity }} porsi</p></div>
                        </div>
                        <div class="flex gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900"><span>&#128205;</span><p>Ambil pesanan di <strong>{{ $product->merchant->name }}</strong> sesuai waktu pengambilan.</p></div>
                    </div>
                </section>

                <section class="h-fit rounded-3xl bg-white p-5 shadow-[0_18px_50px_rgba(15,23,42,0.08)] ring-1 ring-slate-200/70 sm:p-7">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-5"><div><p class="text-xs font-bold uppercase tracking-[0.16em] text-slate-400">Ringkasan</p><h2 class="mt-1 text-xl font-black">Detail pembayaran</h2></div><span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-700">{{ $quantity }}x</span></div>

                    <form id="checkout-form" class="mt-6 space-y-5">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="{{ $quantity }}">

                        <div class="space-y-2">
                            <label for="payment_method" class="block text-sm font-bold text-slate-700">Metode pembayaran</label>
                            @php($paymentLabels = ['SP' => 'QRIS', 'NQ' => 'QRIS Nobu', 'GQ' => 'QRIS Gudang Voucher', 'SQ' => 'QRIS Nusapay', 'BC' => 'BCA Virtual Account', 'M2' => 'Mandiri Virtual Account', 'BR' => 'BRI Virtual Account', 'I1' => 'BNI Virtual Account', 'BV' => 'BSI Virtual Account', 'BT' => 'Permata Virtual Account', 'VA' => 'Maybank Virtual Account', 'B1' => 'CIMB Niaga Virtual Account', 'A1' => 'ATM Bersama', 'AG' => 'Artha Graha Virtual Account', 'NC' => 'BNC Virtual Account', 'S1' => 'Sahabat Sampoerna Virtual Account', 'DM' => 'Danamon Virtual Account', 'GP' => 'GoPay', 'DA' => 'DANA', 'OV' => 'OVO', 'SA' => 'ShopeePay', 'SL' => 'ShopeePay Account Link', 'OL' => 'OVO Account Link', 'VC' => 'Kartu Kredit', 'JP' => 'Jenius Pay', 'IR' => 'Indomaret', 'FT' => 'Alfamart / Pegadaian'])
                            @php($paymentGroups = ['QRIS' => ['SP', 'NQ', 'GQ', 'SQ'], 'Bank BCA' => ['BC'], 'Bank Mandiri' => ['M2'], 'Bank BRI' => ['BR'], 'Bank BNI' => ['I1'], 'Bank BSI' => ['BV'], 'Bank Permata' => ['BT'], 'Bank Lainnya' => ['VA', 'B1', 'A1', 'AG', 'NC', 'S1', 'DM'], 'E-Wallet' => ['GP', 'DA', 'OV', 'SA', 'SL', 'OL'], 'Lainnya' => ['VC', 'JP', 'IR', 'FT']])
                            <select id="payment_method" name="payment_method" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold text-slate-800 outline-none focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10" aria-label="Pilih metode pembayaran" required>
                                <option value="" disabled selected>Pilih metode pembayaran</option>
                                @foreach($paymentGroups as $group => $groupCodes)
                                    @php($groupMethods = array_values(array_intersect($groupCodes, $paymentMethods)))
                                    @if($groupMethods)
                                        <optgroup label="{{ $group }}">
                                            @foreach($groupMethods as $method)
                                                <option value="{{ $method }}">{{ $paymentLabels[$method] ?? $method }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                                @php($knownMethods = array_merge(...array_values($paymentGroups)))
                                @php($otherMethods = array_values(array_diff($paymentMethods, $knownMethods)))
                                @if($otherMethods)
                                    <optgroup label="Bank Lainnya">
                                        @foreach($otherMethods as $method)
                                            <option value="{{ $method }}">{{ $paymentLabels[$method] ?? $method }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>

                        <label for="customer_name" class="block"><span class="text-sm font-bold text-slate-700">Nama pemesan</span><input id="customer_name" name="customer_name" required value="{{ Auth::user()->name }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold outline-none focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"></label>

                        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 p-4 transition has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50">
                            <input id="use-wallet" type="checkbox" name="use_wallet" value="1" @disabled(!$canUseWallet) class="mt-0.5 h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-sm"><strong class="block">Bayar dengan saldo Save Bite</strong><span class="mt-1 block text-xs text-slate-500">Saldo: <b class="text-emerald-700">Rp {{ number_format($walletBalance) }}</b>@unless($canUseWallet) <span class="text-amber-600">(belum cukup)</span>@endunless</span></span>
                        </label>

                        <div class="space-y-3 border-t border-slate-100 pt-5 text-sm"><div class="flex justify-between text-slate-500"><span>Harga makanan</span><span>Rp {{ number_format($subtotal) }}</span></div><div class="flex justify-between text-slate-500"><span>Biaya layanan</span><span>Rp {{ number_format($adminFee) }}</span></div><div class="flex items-end justify-between border-t border-slate-100 pt-4"><span class="font-black">Total bayar</span><span class="text-2xl font-black text-emerald-700">Rp {{ number_format($totalPrice) }}</span></div></div>

                        <button id="pay" type="submit" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-4 text-sm font-black text-white shadow-lg shadow-emerald-700/20 transition hover:bg-emerald-800 disabled:cursor-wait disabled:opacity-60"><span>{{ $canUseWallet ? 'Pilih metode pembayaran' : 'Bayar dengan Duitku' }}</span><span>&rarr;</span></button>
                        <p id="payment-hint" class="text-center text-xs text-slate-400">{{ $canUseWallet ? 'Centang saldo Save Bite untuk membayar langsung, atau lanjut dengan Duitku.' : 'Saldo belum cukup, pembayaran akan dilanjutkan melalui Duitku.' }}</p>
                    </form>
                </section>
            </div>
        </main>
    </div>

    <script>
        const form = document.querySelector('#checkout-form');
        const wallet = document.querySelector('#use-wallet');
        const paymentMethod = document.querySelector('#payment_method');
        const button = document.querySelector('#pay');
        const hint = document.querySelector('#payment-hint');

        const updatePaymentLabel = () => {
            const selected = paymentMethod?.selectedOptions?.[0]?.textContent ?? 'Duitku';
            if (!paymentMethod?.value) {
                button.querySelector('span').textContent = wallet?.checked ? 'Bayar dengan saldo Save Bite' : 'Pilih metode pembayaran';
                hint.textContent = wallet?.checked ? 'Saldo akan langsung dipotong setelah kamu menekan tombol.' : 'Pilih metode pembayaran sebelum lanjut.';
                return;
            }

            button.querySelector('span').textContent = wallet?.checked ? 'Bayar dengan saldo Save Bite' : `Bayar via ${selected}`;
            hint.textContent = wallet?.checked ? 'Saldo akan langsung dipotong setelah kamu menekan tombol.' : `Kamu akan diarahkan ke pembayaran ${selected}.`;
        };

        paymentMethod?.addEventListener('change', updatePaymentLabel);

        wallet?.addEventListener('change', () => {
            updatePaymentLabel();
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            button.disabled = true;
            button.querySelector('span').textContent = 'Memproses pembayaran...';
            try {
                const response = await fetch('{{ route('checkout.process') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: new FormData(form) });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Checkout gagal.');
                window.location.href = result.redirect || result.payment_url || '{{ route('orders.history') }}';
            } catch (error) {
                alert(error.message);
                button.disabled = false;
                button.querySelector('span').textContent = wallet?.checked ? 'Bayar dengan saldo Save Bite' : 'Bayar dengan Duitku';
            }
        });
    </script>
</body>
</html>
