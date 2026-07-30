<?php

namespace App\Services;

use MercadoPago\Client\PreApproval\PreApprovalClient;
use MercadoPago\MercadoPagoConfig;
use App\Models\Plan;
use App\Models\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionService
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
    }

    public function createSubscription(Plan $plan, Client $client, string $interval = 'monthly'): array
    {
        $preapprovalClient = new PreApprovalClient();

        $frequencyData = match ($interval) {
            'semiannual' => ['frequency' => 6, 'frequency_type' => 'months'],
            'annual' => ['frequency' => 12, 'frequency_type' => 'months'],
            default => ['frequency' => 1, 'frequency_type' => 'months'],
        };

        $data = [
            'payer_email' => $client->email,
            'reason' => "Plano {$plan->name}",
            'external_reference' => (string) Str::uuid(),
            'auto_recurring' => [
                'frequency' => $frequencyData['frequency'],
                'frequency_type' => $frequencyData['frequency_type'],
                'transaction_amount' => (float) $plan->price,
                'currency_id' => 'BRL',
            ],
            'back_url' => route('company.plans.success', $plan->id),
            'notification_url' => config('services.mercadopago.webhook_url'),
        ];

        try {
            Log::info('MP preapproval create', $data);

            $preapproval = $preapprovalClient->create($data);

            Log::info('MP preapproval created', [
                'id' => $preapproval->id,
                'status' => $preapproval->status,
                'init_point' => $preapproval->init_point,
            ]);

            return [
                'preapproval_id' => $preapproval->id,
                'status' => $preapproval->status,
                'init_point' => $preapproval->init_point,
            ];

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            $content = $e->getApiResponse()->getContent();
            Log::error('MP preapproval error', ['error' => $content]);
            throw new \RuntimeException($content['message'] ?? 'Erro ao criar assinatura');
        }
    }
}
