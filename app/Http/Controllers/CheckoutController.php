<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\PaymentConfig;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:client');
    }

    public function index()
    {
        $cart = $this->getCart();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('shop.cart')->with('error', 'Carrinho vazio.');
        }

        $cart->load('items.product.images');
        $total = $cart->items->sum(fn($i) => $i->price * $i->quantity);

        $companyId = Auth::guard('client')->user()->company_id;
        $configs = PaymentConfig::where('company_id', $companyId)
            ->where('active', true)
            ->get()
            ->keyBy('method');

        $methods = [];
        foreach (['pix', 'credit_card', 'debit_card'] as $method) {
            $cfg = $configs[$method] ?? null;
            $discountPct = $cfg ? (float) $cfg->discount_percent : 0;
            $discount = round($total * $discountPct / 100, 2);
            $methods[$method] = [
                'label' => match($method) { 'pix' => 'PIX', 'credit_card' => 'Cartão de Crédito', 'debit_card' => 'Cartão de Débito' },
                'discount_percent' => $discountPct,
                'discount' => $discount,
                'final' => $total - $discount,
                'active' => $cfg?->active ?? true,
            ];
        }

        return view('shop.checkout', compact('cart', 'total', 'methods'));
    }

    public function pay(Request $request)
    {
        $cart = $this->getCart();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('shop.cart')->with('error', 'Carrinho vazio.');
        }

        $cart->load('items.product');

        $validMethods = ['pix', 'credit_card', 'debit_card'];
        $method = $request->input('payment_method');
        if (!in_array($method, $validMethods)) {
            return back()->with('error', 'Método de pagamento inválido.');
        }

        // Calcula desconto
        $total = $cart->items->sum(fn($i) => $i->price * $i->quantity);
        $companyId = Auth::guard('client')->user()->company_id;
        $cfg = PaymentConfig::where('company_id', $companyId)
            ->where('method', $method)->where('active', true)->first();
        $discountPct = $cfg ? (float) $cfg->discount_percent : 0;
        $discount = round($total * $discountPct / 100, 2);

        // Dados do cartão (se aplicável)
        $cardData = [];
        if (in_array($method, ['credit_card', 'debit_card'])) {
            $request->validate([
                'token' => ['required', 'string'],
                'installments' => ['required', 'integer', 'min:1', 'max:12'],
                'payment_method_id' => ['required', 'string'],
            ]);
            $cardData = [
                'token' => $request->token,
                'installments' => (int) $request->installments,
                'payment_method_id' => $request->payment_method_id,
            ];
        }

        try {
            $service = new OrderService();
            $result = $service->createOrder($cart, $method, $cardData, $discount);

            // Limpa carrinho após pagamento
            $cart->items()->delete();

            if ($method === 'pix') {
                return view('shop.payment-pix', [
                    'qr_code' => $result['qr_code'],
                    'qr_code_base64' => $result['qr_code_base64'],
                    'external_reference' => $cart->external_reference,
                ]);
            }

            if ($result['success']) {
                return redirect()->route('shop.payment.success', ['ref' => $cart->external_reference]);
            }

            return redirect()->route('shop.payment.failure', ['ref' => $cart->external_reference]);

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $ref = $request->ref;
        return view('shop.payment-success', compact('ref'));
    }

    public function failure(Request $request)
    {
        $ref = $request->ref;
        return view('shop.payment-failure', compact('ref'));
    }

    private function getCart(): ?Cart
    {
        $client = Auth::guard('client')->user();
        return Cart::where('client_id', $client->id)->first();
    }
}
