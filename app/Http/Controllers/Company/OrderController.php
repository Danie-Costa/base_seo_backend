<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Services\CompanyService;
use App\Models\Payment;

class OrderController extends Controller
{
    public function index()
    {
        $company = (new CompanyService)->myCompany();
        $payments = Payment::where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('company.orders.index', compact('payments'));
    }
}
