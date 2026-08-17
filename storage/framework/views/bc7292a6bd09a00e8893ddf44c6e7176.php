<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Booking - <?php echo e($order->booking_code); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .ticket-cut {
            background-image: radial-gradient(circle at 0 50%, transparent 12px, white 13px), radial-gradient(circle at 100% 50%, transparent 12px, white 13px);
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-800 min-h-screen flex items-center justify-center p-4 antialiased">

    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-100">
        <!-- Ticket Header Animated -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-6 text-center relative">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto text-2xl mb-2 backdrop-blur-md animate-bounce">
                🎉
            </div>
            <h1 class="text-xl font-black">Booking Berhasil!</h1>
            <p class="text-xs text-emerald-100 mt-1">Tunjukkan tiket ini kepada pengelola kantin.</p>
        </div>

        <!-- Ticket Body dengan Notch Potongan Struk -->
        <div class="p-6 space-y-5 ticket-cut bg-white relative">
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-center">
                <span class="text-[10px] text-emerald-700 uppercase font-black tracking-widest">KODE BOOKING UNIK</span>
                <p class="text-3xl font-black text-emerald-800 tracking-wider my-1"><?php echo e($order->booking_code); ?></p>
                <span class="text-xs text-slate-500">Pemesan: <b><?php echo e($order->customer_name); ?></b></span>
            </div>

            <div class="border-t border-dashed border-slate-200 pt-4 text-xs space-y-2.5">
                <div class="flex justify-between">
                    <span class="text-slate-400">Item Makanan:</span>
                    <span class="font-bold text-slate-800"><?php echo e($order->product->title); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Kantin / Mitra:</span>
                    <span class="font-bold text-slate-800"><?php echo e($order->product->merchant->name); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Lokasi:</span>
                    <span class="font-bold text-slate-800"><?php echo e($order->product->merchant->location); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Jam Pengambilan:</span>
                    <span class="font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded"><?php echo e($order->product->pickup_time); ?></span>
                </div>
                <div class="flex justify-between border-t pt-2">
                    <span class="text-slate-400">Total Pembayaran:</span>
                    <span class="font-black text-emerald-700 text-sm">Rp <?php echo e(number_format($order->total_price)); ?></span>
                </div>
            </div>

            <!-- Action Button WhatsApp -->
            <a href="https://wa.me/<?php echo e($order->product->merchant->whatsapp); ?>?text=Halo%20<?php echo e(urlencode($order->product->merchant->name)); ?>,%20saya%20mau%20konfirmasi%20booking%20makanan%20dengan%20kode%20<?php echo e($order->booking_code); ?>" 
               target="_blank"
               class="flex items-center justify-center gap-2 w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black py-4 rounded-2xl shadow-lg shadow-emerald-600/30 transition transform active:scale-95">
               <span>💬 Chat WhatsApp Merchant</span>
            </a>

            <a href="<?php echo e(route('home')); ?>" class="block text-center text-xs text-slate-400 hover:text-slate-600">
                &larr; Kembali ke Katalog Utama
            </a>
        </div>
    </div>

</body>
</html><?php /**PATH C:\xampp\htdocs\surplusbite\resources\views/ticket.blade.php ENDPATH**/ ?>