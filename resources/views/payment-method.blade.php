<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Pilih Metode Pembayaran - Save Bite</title>
</head>
<body class="min-h-screen bg-slate-100 px-4 py-8 text-slate-900">
    <main class="mx-auto max-w-md rounded-3xl bg-white p-6 shadow-xl ring-1 ring-slate-200 sm:p-8">
        <p class="text-xs font-black uppercase tracking-[0.16em] text-emerald-700">Pembayaran belum selesai</p>
        <h1 class="mt-2 text-2xl font-black">Pilih metode pembayaran</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Pilih kembali metode untuk pesanan {{ $order->booking_code }}.</p>

        <form action="{{ route('payment.page', $order->id) }}" method="get" class="mt-6 space-y-4">
            <label for="payment_method" class="block text-sm font-bold text-slate-700">Metode pembayaran</label>
            @php($paymentLabels = ['SP' => 'QRIS', 'NQ' => 'QRIS Nobu', 'GQ' => 'QRIS Gudang Voucher', 'SQ' => 'QRIS Nusapay', 'BC' => 'BCA Virtual Account', 'M2' => 'Mandiri Virtual Account', 'BR' => 'BRI Virtual Account', 'I1' => 'BNI Virtual Account', 'BV' => 'BSI Virtual Account', 'BT' => 'Permata Virtual Account', 'VA' => 'Maybank Virtual Account', 'B1' => 'CIMB Niaga Virtual Account', 'A1' => 'ATM Bersama', 'AG' => 'Artha Graha Virtual Account', 'NC' => 'BNC Virtual Account', 'S1' => 'Sahabat Sampoerna Virtual Account', 'DM' => 'Danamon Virtual Account', 'GP' => 'GoPay', 'DA' => 'DANA', 'OV' => 'OVO', 'SA' => 'ShopeePay', 'SL' => 'ShopeePay Account Link', 'OL' => 'OVO Account Link', 'VC' => 'Kartu Kredit', 'JP' => 'Jenius Pay', 'IR' => 'Indomaret', 'FT' => 'Alfamart / Pegadaian'])
            @php($paymentGroups = ['QRIS' => ['SP', 'NQ', 'GQ', 'SQ'], 'Bank BCA' => ['BC'], 'Bank Mandiri' => ['M2'], 'Bank BRI' => ['BR'], 'Bank BNI' => ['I1'], 'Bank BSI' => ['BV'], 'Bank Permata' => ['BT'], 'Bank Lainnya' => ['VA', 'B1', 'A1', 'AG', 'NC', 'S1', 'DM'], 'E-Wallet' => ['GP', 'DA', 'OV', 'SA', 'SL', 'OL'], 'Lainnya' => ['VC', 'JP', 'IR', 'FT']])
            <select id="payment_method" name="payment_method" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm font-semibold outline-none focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
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
            <button type="submit" class="w-full rounded-2xl bg-emerald-700 px-5 py-4 text-sm font-black text-white transition hover:bg-emerald-800">Lanjutkan pembayaran</button>
        </form>

        <a href="{{ route('orders.history') }}" class="mt-4 block text-center text-xs font-bold text-slate-400 hover:text-slate-600">Kembali ke riwayat pesanan</a>
    </main>
</body>
</html>
