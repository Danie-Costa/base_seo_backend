<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class SalesAnalyticsService
{
    public function volumeTotal(): float
    {
        return (float) Payment::where('status', 'approved')->sum('price');
    }

    public function totalOrders(): int
    {
        return Order::count();
    }

    public function paidOrders(): int
    {
        return Order::where('status', 'paid')->count();
    }

    public function pendingPayments(): int
    {
        return Payment::where('status', 'pending')->count();
    }

    public function failedPayments(): int
    {
        return Payment::where('status', 'failure')->count();
    }

    public function topProducts(int $limit = 5): \Illuminate\Support\Collection
    {
        return DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->join('carts', 'cart_items.cart_id', '=', 'carts.id')
            ->join('orders', 'carts.id', '=', 'orders.cart_id')
            ->where('orders.status', 'paid')
            ->select(
                'products.name',
                DB::raw('SUM(cart_items.quantity) as total_qtd'),
                DB::raw('SUM(cart_items.price * cart_items.quantity) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qtd')
            ->limit($limit)
            ->get();
    }

    public function revenueByMonth(): \Illuminate\Support\Collection
    {
        return Payment::where('status', 'approved')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(price) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    public function pendingPaymentsList(int $limit = 10): \Illuminate\Support\Collection
    {
        return Payment::where('status', 'pending')
            ->with('order')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
