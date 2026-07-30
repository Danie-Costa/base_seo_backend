<?php

namespace App\Services;

use MercadoPago\Client\Order\OrderClient;
use MercadoPago\MercadoPagoConfig;
use App\Models\Cart;
use App\Models\Order as OrderModel;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    private OrderClient $client;

    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        $this->client = new OrderClient();
    }

    /**
     * Cria Order no MP e registra OrderModel + Payment no DB.
     *
     * @param Cart $cart Carrinho com items carregados
     * @param string $paymentMethod 'pix'|'credit_card'|'debit_card'
     * @param array $cardData ['token'=>'...', 'installments'=>1, 'payment_method_id'=>'master']
     * @param float $discount Valor do desconto
     * @return array
     */
    public function createOrder(Cart $cart, string $paymentMethod, array $cardData = [], float $discount = 0): array
    {
        $client = Auth::guard('client')->user();
        $total = $cart->items->sum(fn($i) => $i->price * $i->quantity);
        $finalAmount = $total - $discount;

        $cart->load('items.product');

        // Monta transação
        $paymentTx = [
            'amount' => (string) number_format($finalAmount, 2, '.', ''),
            'payment_method' => [],
        ];

        if (in_array($paymentMethod, ['credit_card', 'debit_card'])) {
            $paymentTx['payment_method'] = [
                'id' => $cardData['payment_method_id'],
                'type' => $paymentMethod,
                'token' => $cardData['token'],
                'installments' => (int) ($cardData['installments'] ?? 1),
            ];
        } else {
            $paymentTx['payment_method'] = [
                'type' => 'pix',
            ];
        }

        $orderData = [
            'type' => 'online',
            'processing_mode' => 'automatic',
            'external_reference' => (string) Str::uuid(),
            'total_amount' => (string) number_format($finalAmount, 2, '.', ''),
            'payer' => [
                'email' => $client->email,
            ],
            'transactions' => [
                'payments' => [$paymentTx],
            ],
            'items' => $cart->items->map(fn($i) => [
                'title' => $i->product->name,
                'quantity' => $i->quantity,
                'unit_price' => (float) $i->price,
            ])->toArray(),
        ];

        // Desconto por método de pagamento
        if ($discount > 0) {
            $orderData['discounts'] = [
                ['value' => (float) $discount, 'description' => "Desconto {$paymentMethod}"],
            ];
        }

        try {
            Log::info('MP Order create', ['payload' => $orderData]);
            $mpOrder = $this->client->create($orderData);
            Log::info('MP Order created', ['id' => $mpOrder->id, 'status' => $mpOrder->status]);

            // Salva OrderModel
            $orderModel = OrderModel::create([
                'cart_id' => $cart->id,
                'external_reference' => $orderData['external_reference'],
                'total' => $total,
                'total_discount' => $discount,
                'status' => $this->normalizeOrderStatus($mpOrder->status),
            ]);

            // Dados do primeiro payment transaction
            $tx = $mpOrder->transactions->payments[0] ?? null;

            // PIX QR code
            $qrCode = $mpOrder->type_response?->qr_code ?? null;
            $qrCodeBase64 = $mpOrder->type_response?->qr_code_base64 ?? null;

            Payment::create([
                'order_id' => $orderModel->id,
                'company_id' => $client->company_id,
                'title' => 'Pedido #' . $orderModel->id,
                'price' => $finalAmount,
                'fee' => 0,
                'price_fee' => $finalAmount,
                'status' => $this->normalizePaymentStatus($mpOrder->status),
                'return_type' => 'webhook',
                'external_reference' => $orderData['external_reference'],
                'payment_id' => $tx?->id,
                'payment_type' => $paymentMethod,
                'payment_method_id' => $tx?->payment_method->id ?? null,
                'qr_code' => $qrCode,
                'qr_code_base64' => $qrCodeBase64,
            ]);

            return [
                'success' => in_array($mpOrder->status, ['processed', 'accredited']),
                'mp_order_id' => $mpOrder->id,
                'status' => $mpOrder->status,
                'status_detail' => $mpOrder->status_detail,
                'qr_code' => $qrCode,
                'qr_code_base64' => $qrCodeBase64,
                'payment_id' => $tx?->id,
            ];

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            $content = $e->getApiResponse()->getContent();
            $msg = $content['message'] ?? 'Erro ao processar pagamento';
            Log::error('MP Order API error', ['error' => $content]);
            throw new \RuntimeException($msg);

        } catch (\Throwable $e) {
            Log::error('MP Order unexpected error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Busca uma Order no MP pelo ID.
     */
    public function getOrder(string $mpOrderId): \MercadoPago\Resources\Order
    {
        return $this->client->get($mpOrderId);
    }

    /**
     * Processa webhook de notificação de Order.
     */
    public function processWebhook(array $data): ?Payment
    {
        $type = $data['type'] ?? '';
        $resourceId = $data['data']['id'] ?? null;

        if ($type !== 'order' || !$resourceId) {
            return null;
        }

        try {
            $mpOrder = $this->getOrder($resourceId);
        } catch (\Throwable $e) {
            Log::warning('MP webhook: order not found', ['id' => $resourceId]);
            return null;
        }

        $payment = Payment::where('external_reference', $mpOrder->external_reference)->first();
        if (!$payment) {
            Log::warning('MP webhook: payment not found', ['ext_ref' => $mpOrder->external_reference]);
            return null;
        }

        $paymentStatus = $this->normalizePaymentStatus($mpOrder->status);
        $tx = $mpOrder->transactions->payments[0] ?? null;

        $payment->update([
            'status' => $paymentStatus,
            'payment_id' => $tx?->id ?? $payment->payment_id,
            'payment_method_id' => $tx?->payment_method->id ?? $payment->payment_method_id,
            'qr_code' => $mpOrder->type_response?->qr_code ?? $payment->qr_code,
            'qr_code_base64' => $mpOrder->type_response?->qr_code_base64 ?? $payment->qr_code_base64,
        ]);

        if ($payment->order) {
            $orderStatus = $paymentStatus === 'approved' ? 'paid' : 'pending';
            $payment->order->update(['status' => $orderStatus]);
        }

        return $payment;
    }

    private function normalizeOrderStatus(?string $status): string
    {
        return match ($status) {
            'processed', 'accredited' => 'paid',
            'opened' => 'pending',
            default => 'canceled',
        };
    }

    private function normalizePaymentStatus(?string $status): string
    {
        return match ($status) {
            'processed', 'accredited' => 'approved',
            'opened' => 'pending',
            default => 'failure',
        };
    }
}
