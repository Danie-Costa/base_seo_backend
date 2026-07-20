<?php

namespace App\Services;

use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    private string $accessToken;

    public function __construct(?string $accessToken = null)
    {
        $this->accessToken = $accessToken ?? config('services.mercadopago.access_token');
        MercadoPagoConfig::setAccessToken($this->accessToken);
    }

    public function createPix(Payment $payment, array $payer): array
    {
        return $this->createPayment($payment, $payer, 'pix');
    }

    public function createCard(Payment $payment, array $cardData, array $payer): array
    {
        $data = $this->buildPaymentData($payment, $payer, 'credit_card');
        $data['token'] = $cardData['token'];
        $data['installments'] = (int) ($cardData['installments'] ?? 1);

        return $this->execute($payment, $data);
    }

    public function createBoleto(Payment $payment, array $payer): array
    {
        return $this->createPayment($payment, $payer, 'bolbradesco');
    }

    public function getPayment(int $paymentId): array
    {
        $client = new PaymentClient();
        $mpPayment = $client->get($paymentId);

        return [
            'id' => $mpPayment->id,
            'status' => $mpPayment->status,
            'status_detail' => $mpPayment->status_detail,
            'payment_method_id' => $mpPayment->payment_method_id,
            'transaction_amount' => $mpPayment->transaction_amount,
            'payer' => $mpPayment->payer ?? null,
        ];
    }

    public function processWebhook(array $data): ?Payment
    {
        $type = $data['type'] ?? '';
        $resourceId = $data['data']['id'] ?? null;

        if ($type !== 'payment' || !$resourceId) {
            return null;
        }

        $mpPayment = $this->getPayment($resourceId);

        $payment = Payment::where('payment_id', $resourceId)->first();
        if (!$payment) return null;

        $status = match ($mpPayment['status']) {
            'approved' => 'approved',
            'pending', 'in_process', 'in_mediation' => 'pending',
            'rejected', 'cancelled', 'refunded', 'charged_back' => 'failure',
            default => 'pending',
        };

        $payment->update([
            'status' => $status,
            'payment_method_id' => $mpPayment['payment_method_id'] ?? $payment->payment_method_id,
        ]);

        if ($payment->order) {
            $orderStatus = $status === 'approved' ? 'paid' : ($status === 'failure' ? 'canceled' : 'pending');
            $payment->order->update(['status' => $orderStatus]);
        }

        return $payment;
    }

    // ── Internals ──

    private function createPayment(Payment $payment, array $payer, string $methodId): array
    {
        $data = $this->buildPaymentData($payment, $payer, $methodId);
        return $this->execute($payment, $data);
    }

    private function execute(Payment $payment, array $data): array
    {
        try {
            Log::info('MP create payment', ['payload' => $data]);

            $client = new PaymentClient();
            $mpPayment = $client->create($data);

            $payment->update([
                'payment_id' => (string) $mpPayment->id,
                'status' => $this->normalizeStatus($mpPayment->status),
                'payment_method_id' => $mpPayment->payment_method_id ?? null,
                'payment_type' => $data['payment_method_id'] ?? 'credit_card',
                'qr_code' => $mpPayment->point_of_interaction?->transaction_data?->qr_code ?? null,
                'qr_code_base64' => $mpPayment->point_of_interaction?->transaction_data?->qr_code_base64 ?? null,
                'ticket_url' => $mpPayment->point_of_interaction?->transaction_data?->ticket_url
                    ?? $mpPayment->transaction_details?->external_resource_url ?? null,
            ]);

            Log::info('MP payment created', ['id' => $mpPayment->id, 'status' => $mpPayment->status]);

            return [
                'approved' => $mpPayment->status === 'approved',
                'status' => $mpPayment->status,
                'payment_id' => (string) $mpPayment->id,
                'qr_code' => $payment->refresh()->qr_code,
                'qr_code_base64' => $payment->qr_code_base64,
                'ticket_url' => $payment->ticket_url,
                'payment_method_id' => $mpPayment->payment_method_id ?? null,
            ];

        } catch (MPApiException $e) {
            $error = $e->getApiResponse()->getContent();
            Log::error('MP API error', ['error' => $error]);

            throw new \RuntimeException(
                'Erro ao processar pagamento: ' . ($error['message'] ?? 'erro desconhecido'),
                $e->getCode(),
                $e
            );
        } catch (\Throwable $e) {
            Log::error('MP unexpected error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function buildPaymentData(Payment $payment, array $payer, string $methodId): array
    {
        $data = [
            'transaction_amount' => (float) $payment->price,
            'description' => $payment->title,
            'payment_method_id' => $methodId,
            'payer' => [
                'email' => $payer['email'] ?? 'comprador@email.com',
                'first_name' => $payer['first_name'] ?? $payer['name'] ?? 'Comprador',
                'last_name' => $payer['last_name'] ?? '',
                'identification' => [
                    'type' => $payer['identification_type'] ?? 'CPF',
                    'number' => $payer['identification_number'] ?? '00000000000',
                ],
            ],
            'external_reference' => (string) $payment->id,
        ];

        // Notification_url opcional — MP rejeita localhost
        $webhook = $payment->webhook_url ?? config('services.mercadopago.webhook_url');
        if ($webhook && !str_contains($webhook, 'localhost') && !str_contains($webhook, '127.0.0.1')) {
            $data['notification_url'] = $webhook;
        }

        // PIX e cartão não enviam payment_method_id (API determina)
        if (in_array($methodId, ['pix', 'credit_card'])) {
            unset($data['payment_method_id']);
        }

        return $data;
    }

    private function normalizeStatus(string $status): string
    {
        return match ($status) {
            'approved' => 'approved',
            'pending', 'in_process', 'in_mediation' => 'pending',
            default => 'failure',
        };
    }
}
