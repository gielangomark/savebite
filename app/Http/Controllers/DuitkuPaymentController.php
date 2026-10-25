<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Models\Product;
use App\Services\DuitkuService;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class DuitkuPaymentController extends Controller {
    public function checkout(Request $request, Product $product, DuitkuService $duitku) { abort_if(!$product->is_active || $product->stock < 1, 422, 'Menu sedang tidak tersedia.'); $quantity=max(1,(int)$request->integer('quantity',1)); $subtotal=$product->discount_price*$quantity; $adminFee=$subtotal<100000?3000:5000; $totalPrice=$subtotal+$adminFee; $paymentMethods=$duitku->selectablePaymentMethods($totalPrice); return view('checkout', compact('product','quantity','subtotal','adminFee','totalPrice','paymentMethods')); }
    public function process(Request $request, DuitkuService $duitku, WalletService $wallet) {
        $data=$request->validate(['product_id'=>['required','exists:products,id'],'customer_name'=>['required','string','max:255'],'quantity'=>['required','integer','min:1'],'payment_method'=>['nullable','string','max:32'],'use_wallet'=>['nullable','boolean']]);
        $order=DB::transaction(function () use($data) { $product=Product::lockForUpdate()->findOrFail($data['product_id']); abort_if(!$product->is_active || $product->stock<$data['quantity'],422,'Menu habis atau tidak aktif.'); $subtotal=$product->discount_price*$data['quantity']; $fee=$subtotal<100000?3000:5000; $end=$product->pickup_end_time ?: '23:59:00'; $expires=Carbon::today($product->merchant->timezone ?: 'Asia/Jakarta')->setTimeFromTimeString($end)->utc(); if($expires->isPast()) $expires->addDay(); return Order::create(['user_id'=>Auth::id(),'product_id'=>$product->id,'booking_code'=>'SB-'.strtoupper(Str::random(10)),'customer_name'=>$data['customer_name'],'customer_phone'=>Auth::user()->phone,'quantity'=>$data['quantity'],'total_price'=>$subtotal+$fee,'subtotal_amount'=>$subtotal,'platform_fee'=>$fee,'merchant_amount'=>$subtotal,'status'=>'pending','payment_gateway'=>'duitku','payment_method'=>$data['payment_method'] ?? null,'pickup_expires_at'=>$expires]); });
        if ($request->boolean('use_wallet')) { $wallet->debit(Auth::user(), $order->total_price, 'order-wallet-'.$order->id, 'Pembayaran pesanan '.$order->booking_code, $order); $this->markPaid($order, 'wallet'); return response()->json(['status'=>'success','redirect'=>route('ticket',$order->booking_code)]); }
        try { $invoice=$duitku->createInvoice($order, $data['payment_method'] ?? null); $order->update(['gateway_reference'=>$invoice['reference'] ?? null]); return response()->json(['status'=>'pending','payment_url'=>$invoice['paymentUrl'] ?? $invoice['payment_url'] ?? null]); } catch(\Throwable $e) { $order->delete(); return response()->json(['message'=>$e->getMessage()],422); }
    }
    public function callback(Request $request) { $data=$request->all(); if(!app(DuitkuService::class)->validCallback($data)) return response()->json(['message'=>'Invalid signature'],403); $order=Order::where('booking_code',$data['merchantOrderId'] ?? '')->firstOrFail(); if(($data['resultCode'] ?? '')==='00') $this->markPaid($order, $data['paymentCode'] ?? 'duitku'); else $order->update(['status'=>'cancelled']); return response()->json(['statusCode'=>'00']); }
    public function cancel(Order $order, WalletService $wallet) { abort_unless($order->user_id === Auth::id(), 403); abort_unless($order->status === 'ready_for_pickup', 422, 'Pesanan ini tidak dapat dibatalkan.'); DB::transaction(function () use ($order, $wallet) { $order->refresh(); if ($order->refunded_at) return; $wallet->credit($order->user, (int) $order->total_price, 'refund-cancel-'.$order->id, 'Refund pembatalan '.$order->booking_code, $order); $order->product->increment('stock', $order->quantity); $order->update(['status'=>'cancelled_refunded','refunded_at'=>now()]); }); return back()->with('status','Pesanan dibatalkan. Refund masuk ke saldo Save Bite.'); }
    private function markPaid(Order $order, string $method): void { DB::transaction(function() use($order,$method) { $order->refresh(); if(!in_array($order->status,['pending','unpaid'])) return; $order->product()->decrement('stock',$order->quantity); $order->update(['status'=>'ready_for_pickup','payment_method'=>$method,'claim_code'=>'SB-'.strtoupper(Str::random(6))]); }); }
}
