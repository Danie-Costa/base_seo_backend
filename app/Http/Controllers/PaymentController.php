<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function checkout($id)
    {
        $payment = Payment::with('order')->findOrFail($id);
        return view('payment.checkout', compact('payment'));
    }

    public function processPix(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'cpf' => ['required', 'string', 'max:14'],
        ]);

        try {
            $service = new PaymentService();
            $result = $service->createPix($payment, [
                'email' => $request->email,
                'name' => $request->name,
                'first_name' => explode(' ', $request->name)[0],
                'last_name' => explode(' ', $request->name)[1] ?? '',
                'identification_type' => 'CPF',
                'identification_number' => preg_replace('/\D/', '', $request->cpf),
            ]);

            $payment->update(['payment_type' => 'pix', 'payer' => $request->only('name', 'email', 'cpf')]);

            return view('payment.pix', [
                'payment' => $payment,
                'qr_code' => $result['qr_code'],
                'qr_code_base64' => $result['qr_code_base64'],
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function processCard(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'token' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'cpf' => ['required', 'string', 'max:14'],
            'installments' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        try {
            $service = new PaymentService();
            $result = $service->createCard($payment, [
                'token' => $request->token,
                'installments' => $request->installments,
            ], [
                'email' => $request->email,
                'name' => $request->name,
                'first_name' => explode(' ', $request->name)[0],
                'last_name' => explode(' ', $request->name)[1] ?? '',
                'identification_type' => 'CPF',
                'identification_number' => preg_replace('/\D/', '', $request->cpf),
            ]);

            $payment->update(['payment_type' => 'credit_card', 'payer' => $request->only('name', 'email', 'cpf')]);

            if ($result['approved']) {
                return redirect()->route('payment.success', $payment->id);
            }

            return redirect()->route('payment.failure', $payment->id);

        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function processBoleto(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'cpf' => ['required', 'string', 'max:14'],
        ]);

        try {
            $service = new PaymentService();
            $result = $service->createBoleto($payment, [
                'email' => $request->email,
                'name' => $request->name,
                'first_name' => explode(' ', $request->name)[0],
                'last_name' => explode(' ', $request->name)[1] ?? '',
                'identification_type' => 'CPF',
                'identification_number' => preg_replace('/\D/', '', $request->cpf),
            ]);

            $payment->update(['payment_type' => 'boleto', 'payer' => $request->only('name', 'email', 'cpf')]);

            return view('payment.boleto', [
                'payment' => $payment,
                'ticket_url' => $result['ticket_url'],
            ]);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function success($id)
    {
        $payment = Payment::findOrFail($id);
        return view('payment.success', compact('payment'));
    }

    public function failure($id)
    {
        $payment = Payment::findOrFail($id);
        return view('payment.failure', compact('payment'));
    }
}
