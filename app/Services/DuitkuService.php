<?php
namespace App\Services;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
class DuitkuService {
    private function base(): string { return config('duitku.is_production') ? 'https://passport.duitku.com' : 'https://sandbox.duitku.com'; }
    public function createInvoice(Order $order, ?string $paymentMethod = null): array {
        $code=config('duitku.merchant_code'); $key=config('duitku.api_key'); if (!$code || !$key) throw new \RuntimeException('Duitku belum dikonfigurasi. Isi DUITKU_MERCHANT_CODE dan DUITKU_API_KEY.');

        $paymentMethod = $this->resolvePaymentMethod($order, $paymentMethod);

        $payload=[
            'merchantCode'=>$code,
            'paymentAmount'=>$order->total_price,
            'merchantOrderId'=>$order->booking_code,
            'productDetails'=>$order->product->title,
            'email'=>$order->user->email,
            'customerVaName'=>$order->customer_name,
            'paymentMethod'=>$paymentMethod,
            'callbackUrl'=>route('duitku.callback'),
            'returnUrl'=>route('orders.history'),
            'expiryPeriod'=>60,
        ];

        $payload['signature']=hash_hmac('sha256', $code.$order->booking_code.$order->total_price, $key);
        return Http::acceptJson()->asJson()->post($this->base().'/webapi/api/merchant/v2/inquiry', $payload)->throw()->json();
    }

    public function getTransactionStatus(Order $order): array {
        $code=config('duitku.merchant_code'); $key=config('duitku.api_key'); if (!$code || !$key) throw new \RuntimeException('Duitku belum dikonfigurasi. Isi DUITKU_MERCHANT_CODE dan DUITKU_API_KEY.');

        $payload=[
            'merchantCode'=>$code,
            'merchantOrderId'=>$order->booking_code,
            'signature'=>hash_hmac('sha256', $code.$order->booking_code, $key),
        ];

        return Http::acceptJson()->asJson()->post($this->base().'/webapi/api/merchant/transactionStatus', $payload)->throw()->json();
    }

    public function selectablePaymentMethods(int $amount): array {
        $activeMethods = $this->getAvailablePaymentMethods($amount);

        return !empty($activeMethods) ? $activeMethods : ['SP', 'BC', 'DA', 'OV', 'SA', 'JP'];
    }

    private function resolvePaymentMethod(Order $order, ?string $paymentMethod): string {
        $normalized = trim((string) ($paymentMethod ?: $order->payment_method ?: config('duitku.default_payment_method', 'BC')));
        if ($normalized === '') {
            return 'BC';
        }

        $normalized = strtoupper($normalized);

        $mapping = [
            'QRIS' => 'SP',
            'BANK' => 'BC',
            'BCA' => 'BC',
            'VIRTUAL_ACCOUNT' => 'BC',
            'VIRTUALACCOUNT' => 'BC',
            'VA' => 'BC',
        ];

        if (isset($mapping[$normalized])) {
            $normalized = $mapping[$normalized];
        }

        if (!empty($paymentMethod)) {
            return $normalized;
        }

        $activeMethods = $this->getAvailablePaymentMethods($order->total_price);
        if (!empty($activeMethods)) {
            if (in_array($normalized, $activeMethods, true)) {
                return $normalized;
            }

            return $activeMethods[0];
        }

        return $normalized;
    }

    private function getAvailablePaymentMethods(int $amount): array {
        $code=config('duitku.merchant_code'); $key=config('duitku.api_key'); if (!$code || !$key) return [];

        $datetime = now()->format('Y-m-d H:i:s');
        $signature = hash_hmac('sha256', $code . $amount . $datetime, $key);

        try {
            $response = Http::acceptJson()->asJson()->post($this->base().'/webapi/api/merchant/paymentmethod/getpaymentmethod', [
                'merchantcode' => $code,
                'amount' => $amount,
                'datetime' => $datetime,
                'signature' => $signature,
            ]);

            if (!$response->successful()) {
                return [];
            }

            $paymentFee = $response->json('paymentFee') ?? [];
            return array_values(array_filter(array_map(function ($item) {
                return is_array($item) && !empty($item['paymentMethod']) ? strtoupper((string) $item['paymentMethod']) : null;
            }, $paymentFee)));
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function validCallback(array $data): bool { $signature=$data['signature'] ?? ''; $raw=(string)config('duitku.merchant_code').($data['amount'] ?? $data['paymentAmount'] ?? '').($data['merchantOrderId'] ?? ''); return hash_equals(hash_hmac('sha256',$raw,(string) config('duitku.api_key')), $signature) || hash_equals(md5($raw.config('duitku.api_key')), $signature); }
}
