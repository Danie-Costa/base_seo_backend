<?php

namespace App\Http\Controllers;

use App\Services\SalesAnalyticsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $analytics = new SalesAnalyticsService();

        $data = [
            'volumeTotal' => $analytics->volumeTotal(),
            'totalOrders' => $analytics->totalOrders(),
            'paidOrders' => $analytics->paidOrders(),
            'pendingCount' => $analytics->pendingPayments(),
            'failedCount' => $analytics->failedPayments(),
            'topProducts' => $analytics->topProducts(),
            'revenueByMonth' => $analytics->revenueByMonth(),
            'pendingPayments' => $analytics->pendingPaymentsList(),
        ];

        return view('admin.dashboard', $data);
    }

    public function company()
    {
        return view('company.dashboard');
    }
}
