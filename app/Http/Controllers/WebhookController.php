<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function mercadopago(Request $request)
    {
        Log::info('MP webhook received', $request->all());

        $service = new OrderService();
        $payment = $service->processWebhook($request->all());

        return response()->json(['received' => true]);
    }
}
