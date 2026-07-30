<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:client');
    }

    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cart->load('items.product.images');
        return view('shop.cart', compact('cart'));
    }

    public function add(Request $request, Product $product)
    {
        $cart = $this->getOrCreateCart();

        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $item->quantity = ($item->exists ? $item->quantity : 0) + max(1, (int) ($request->qty ?? 1));
        $item->price = $product->price;
        $item->save();

        return redirect()->route('shop.cart')->with('success', 'Produto adicionado ao carrinho!');
    }

    public function remove(CartItem $item)
    {
        $cart = $this->getOrCreateCart();
        if ($item->cart_id !== $cart->id) {
            abort(403);
        }
        $item->delete();
        return back()->with('success', 'Item removido do carrinho.');
    }

    private function getOrCreateCart(): Cart
    {
        $client = Auth::guard('client')->user();
        $company = $client->company_id
            ? \App\Models\Company::find($client->company_id)
            : \App\Models\Company::first();

        return Cart::firstOrCreate(
            ['client_id' => $client->id],
            ['external_reference' => (string) Str::uuid()]
        );
    }
}
