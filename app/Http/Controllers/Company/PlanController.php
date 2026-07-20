<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Services\CompanyService;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function index()
    {
        $company = (new CompanyService)->myCompany();
        $plans = Plan::orderBy('price')->get();

        return view('company.plans.index', compact('company', 'plans'));
    }

    public function checkout(Plan $plan)
    {
        $company = (new CompanyService)->myCompany();

        $price = $this->calcPrice($plan);

        // Cria payment
        $payment = Payment::create([
            'company_id' => $company->id,
            'title' => "Plano {$plan->name} - " . $this->intervalLabel($plan->interval),
            'price' => $price,
            'fee' => 0,
            'price_fee' => $price,
            'status' => 'pending',
            'return_type' => 'webhook',
            'external_reference' => (string) Str::uuid(),
            'redirect_success_url' => route('company.plans.success', $plan->id),
            'redirect_failure_url' => route('company.plans.failure', $plan->id),
        ]);

        return view('payment.checkout', compact('payment'));
    }

    public function success(Plan $plan)
    {
        $company = (new CompanyService)->myCompany();
        $payment = Payment::where('company_id', $company->id)
            ->where('status', 'approved')
            ->latest()
            ->first();

        if ($payment) {
            $this->activatePlan($company, $plan);
        }

        return view('company.plans.success', compact('plan'));
    }

    public function failure(Plan $plan)
    {
        return view('company.plans.failure', compact('plan'));
    }

    public function cancel(Request $request)
    {
        $company = (new CompanyService)->myCompany();

        if ($company->plan_status !== 'active') {
            return back()->with('error', 'Nenhum plano ativo para cancelar.');
        }

        $daysActive = now()->diffInDays($company->plan_started_at);

        if ($daysActive > 7) {
            return back()->with('error', 'Prazo de cancelamento de 7 dias expirado.');
        }

        $company->update([
            'plan_status' => 'canceled',
            'plan_canceled_at' => now(),
        ]);

        return redirect()->route('company.plans.index')->with('success', 'Plano cancelado com sucesso. Reembolso será processado em até 5 dias úteis.');
    }

    // ── Helpers ──

    private function calcPrice(Plan $plan): float
    {
        return match ($plan->interval) {
            'monthly' => (float) $plan->price,
            'semiannual' => (float) $plan->price * 6 * 0.9, // 10% off
            'annual' => (float) $plan->price * 12 * 0.8, // 20% off
        };
    }

    private function intervalLabel(string $interval): string
    {
        return match ($interval) {
            'monthly' => 'Mensal',
            'semiannual' => 'Semestral',
            'annual' => 'Anual',
        };
    }

    private function activatePlan($company, $plan)
    {
        $months = match ($plan->interval) {
            'monthly' => 1,
            'semiannual' => 6,
            'annual' => 12,
        };

        $company->update([
            'plan_id' => $plan->id,
            'plan_status' => 'active',
            'plan_started_at' => now(),
            'plan_expires_at' => now()->addMonths($months),
            'plan_canceled_at' => null,
        ]);
    }
}
