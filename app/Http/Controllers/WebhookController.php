<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function mercadopago(Request $request)
    {
        $service = new PaymentService();
        $payment = $service->processWebhook($request->all());

        return response()->json(['received' => true]);
    }
}
