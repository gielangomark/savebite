<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selesaikan Pembayaran - Save Bite</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Midtrans Snap JS -->
    <script type="text/javascript"
            src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 font-['Plus_Jakarta_Sans']">

    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs max-w-sm w-full text-center space-y-4">
        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl mx-auto">
            💳
        </div>
        
        <div>
            <h2 class="text-base font-extrabold text-slate-900">Membuka Pembayaran...</h2>
            <p class="text-xs text-slate-500 mt-1">Selesaikan pembayaran untuk kode booking:</p>
            <span class="inline-block bg-slate-100 font-mono font-bold text-slate-800 text-xs px-2.5 py-1 rounded-lg mt-1.5">
                {{ $order->booking_code }}
            </span>
        </div>

        <div class="pt-2">
            <button id="pay-button" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition active:scale-95">
                Buka Pop-up Pembayaran
            </button>
            <a href="{{ route('orders.history') }}" class="block text-xs text-slate-400 font-semibold hover:text-slate-600 mt-3 transition">
                Kembali ke Riwayat Pesanan
            </a>
        </div>
    </div>

    <script type="text/javascript">
        function triggerSnap() {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    window.location.href = "{{ route('orders.history') }}";
                },
                onPending: function(result) {
                    window.location.href = "{{ route('orders.history') }}";
                },
                onError: function(result) {
                    alert("Pembayaran gagal!");
                },
                onClose: function() {
                    window.location.href = "{{ route('orders.history') }}";
                }
            });
        }

        // Membuka Pop-up Midtrans OTOMATIS saat halaman terbuka
        window.onload = function() {
            triggerSnap();
        };

        document.getElementById('pay-button').onclick = function() {
            triggerSnap();
        };
    </script>
@include('components.live-clock')
</body>
</html>