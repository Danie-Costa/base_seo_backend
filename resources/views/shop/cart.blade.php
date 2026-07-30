@extends('layouts.public')

@section('content')
<style>
body { background:#0B1D33; }
.cart-card { background:#142C4C; border:1px solid rgba(255,255,255,0.06); border-radius:0.75rem; padding:1.5rem; }
.cart-card h5 { color:#fff; }
.cart-card .item { border-bottom:1px solid rgba(255,255,255,0.06); padding:1rem 0; }
.cart-card .item:last-child { border-bottom:none; }
.cart-card .item-img { width:60px; height:60px; object-fit:cover; border-radius:0.4rem; background:#0B1D33; }
.cart-card .item-name { color:#fff; font-weight:600; }
.cart-card .item-price { color:#D4A017; font-weight:700; }
.cart-card .item-qty { background:#0B1D33; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:0.4rem; padding:0.3rem 0.6rem; width:60px; text-align:center; }
.cart-card .btn-remove { color:#C64545; background:none; border:none; font-size:0.8rem; }
.cart-card .btn-remove:hover { text-decoration:underline; }
.cart-card .total-row { border-top:1px solid rgba(255,255,255,0.1); padding-top:1rem; margin-top:1rem; }
.cart-card .total-row span { color:#E6E6E6; font-weight:600; }
.cart-card .total-row .total-value { color:#D4A017; font-size:1.4rem; font-weight:700; }
.cart-card .btn-primary { background:#D4A017; border:none; color:#0B1D33; font-weight:700; padding:0.75rem 2rem; border-radius:0.5rem; }
.cart-card .btn-primary:hover { background:#e3b239; }
.alert-success { background:rgba(30,158,99,0.1); border:1px solid rgba(30,158,99,0.2); color:#1E9E63; padding:0.75rem; border-radius:0.5rem; margin-bottom:1rem; font-size:0.85rem; }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color:#fff;">Meu Carrinho</h4>
        <a href="{{ route('shop.products') }}" style="color:#D4A017;text-decoration:none;font-size:0.85rem;">
            <i class="fa fa-arrow-left"></i> Continuar comprando
        </a>
    </div>

    @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
    @endif

    @php $cart = $cart ?? null; @endphp

    @if(!$cart || $cart->items->isEmpty())
    <div class="cart-card text-center py-5">
        <div style="font-size:3rem;color:#E6E6E6;opacity:0.3;margin-bottom:1rem;">
            <i class="fa fa-shopping-cart"></i>
        </div>
        <p style="color:#E6E6E6;opacity:0.6;">Seu carrinho está vazio.</p>
        <a href="{{ route('shop.products') }}" class="btn btn-primary mt-2">Ver produtos</a>
    </div>
    @else
    <div class="cart-card">
        @foreach($cart->items as $item)
        <div class="item d-flex align-items-center gap-3">
            @php $img = $item->product->images->first(); @endphp
            <img src="{{ $img ? asset('storage/' . $img->path) : 'https://via.placeholder.com/60?text=+' }}"
                 class="item-img" alt="{{ $item->product->name }}">
            <div class="flex-grow-1">
                <div class="item-name">{{ $item->product->name }}</div>
                <div class="item-price">R$ {{ number_format($item->price, 2, ',', '.') }}</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <input type="number" value="{{ $item->quantity }}" min="1" class="item-qty" readonly>
                <span style="color:#E6E6E6;opacity:0.7;font-size:0.85rem;">
                    = R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                </span>
            </div>
            <form method="POST" action="{{ route('shop.cart.remove', $item) }}" class="m-0">
                @csrf @method('DELETE')
                <button type="submit" class="btn-remove" onclick="return confirm('Remover item?')">
                    <i class="fa fa-trash"></i>
                </button>
            </form>
        </div>
        @endforeach

        <div class="total-row d-flex justify-content-between align-items-center">
            <span>Total</span>
            <div class="d-flex align-items-center gap-3">
                <span class="total-value">R$ {{ number_format($cart->items->sum(fn($i) => $i->price * $i->quantity), 2, ',', '.') }}</span>
                <a href="{{ route('shop.checkout') }}" class="btn btn-primary">Finalizar pedido</a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
