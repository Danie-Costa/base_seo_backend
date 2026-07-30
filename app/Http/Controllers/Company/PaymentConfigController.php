<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Services\CompanyService;
use App\Models\PaymentConfig;
use Illuminate\Http\Request;

class PaymentConfigController extends Controller
{
    public function index()
    {
        $company = (new CompanyService)->myCompany();
        $configs = PaymentConfig::where('company_id', $company->id)->get()->keyBy('method');

        // Garante que as 3 opções existam
        foreach (['pix', 'credit_card', 'debit_card'] as $method) {
            if (!isset($configs[$method])) {
                $configs[$method] = PaymentConfig::create([
                    'company_id' => $company->id,
                    'method' => $method,
                    'discount_percent' => 0,
                    'active' => true,
                ]);
            }
        }

        return view('company.payment-config.index', compact('configs'));
    }

    public function update(Request $request)
    {
        $company = (new CompanyService)->myCompany();

        $data = $request->validate([
            'discount_percent' => ['required', 'array'],
            'discount_percent.pix' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount_percent.credit_card' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount_percent.debit_card' => ['required', 'numeric', 'min:0', 'max:100'],
            'active' => ['required', 'array'],
            'active.pix' => ['boolean'],
            'active.credit_card' => ['boolean'],
            'active.debit_card' => ['boolean'],
        ]);

        foreach (['pix', 'credit_card', 'debit_card'] as $method) {
            PaymentConfig::updateOrCreate(
                ['company_id' => $company->id, 'method' => $method],
                [
                    'discount_percent' => $data['discount_percent'][$method],
                    'active' => $request->boolean("active.{$method}"),
                ]
            );
        }

        return redirect()->route('company.payment-config.index')
            ->with('success', 'Configurações de pagamento salvas!');
    }
}
