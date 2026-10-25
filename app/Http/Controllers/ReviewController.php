<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id'   => ['required', 'exists:orders,id'],
            'product_id' => ['required', 'exists:products,id'],
            'rating'     => ['required', 'integer', 'min:1', 'max:5'],
            'comment'    => ['nullable', 'string', 'max:500'],
        ]);

        $order = Order::findOrFail($validated['order_id']);

        // Keamanan: Hanya pembeli pemilik pesanan yang boleh memberi ulasan
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengulas pesanan ini.');
        }

        // PERBAIKAN: Mengizinkan status 'completed' dan 'served'
        if (!in_array($order->status, ['completed', 'served'])) {
            return back()->with('error', 'Pesanan belum selesai diambil.');
        }

        // Cek jika sudah pernah memberi ulasan
        if ($order->review) {
            return back()->with('error', 'Anda sudah memberi ulasan untuk pesanan ini.');
        }

        Review::create([
            'order_id'   => $order->id,
            'user_id'    => Auth::id(),
            'product_id' => $validated['product_id'],
            'rating'     => $validated['rating'],
            'comment'    => $validated['comment'],
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }
}