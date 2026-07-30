<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function products()
    {
        $products = Product::where('active', true)
            ->with(['images' => function ($q) { $q->orderBy('is_cover', 'desc')->orderBy('order'); }, 'categories'])
            ->orderBy('name')
            ->get();

        return view('shop.products', compact('products'));
    }

    public function detail($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('active', true)
            ->with(['images' => function ($q) { $q->orderBy('is_cover', 'desc')->orderBy('order'); }, 'categories'])
            ->firstOrFail();

        return view('shop.product-detail', compact('product'));
    }
}
