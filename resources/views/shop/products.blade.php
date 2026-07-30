@extends('layouts.public')

@section('content')
<style>
body { background:#0B1D33; }
.product-card { background:#142C4C; border:1px solid rgba(255,255,255,0.06); border-radius:0.75rem; overflow:hidden; transition:transform .2s,box-shadow .2s; }
.product-card:hover { transform:translateY(-2px); box-shadow:0 4px 20px rgba(0,0,0,0.3); }
.product-card .card-img-wrap { height:200px; background:#0B1D33; display:flex; align-items:center; justify-content:center; overflow:hidden; }
.product-card .card-img-wrap img { width:100%; height:100%; object-fit:cover; }
.product-card .card-img-wrap .no-img { color:#E6E6E6; opacity:0.3; font-size:2.5rem; }
.product-card .card-body { padding:1rem; }
.product-card .card-body h6 { color:#fff; font-weight:600; margin-bottom:0.25rem; }
.product-card .card-body .category { color:#E6E6E6; opacity:0.5; font-size:0.75rem; }
.product-card .card-body .price { color:#D4A017; font-weight:700; font-size:1.15rem; }
</style>

<div class="container py-5">
    <h4 class="fw-bold mb-4" style="color:#fff;">Produtos</h4>

    <div class="row g-4">
        @forelse($products as $product)
        <div class="col-md-4 col-lg-3">
            <a href="{{ route('shop.product-detail', $product->slug) }}" class="text-decoration-none">
                <div class="product-card">
                    <div class="card-img-wrap">
                        @php $cover = $product->images->firstWhere('is_cover', true) ?? $product->images->first(); @endphp
                        @if($cover)
                        <img src="{{ asset('storage/' . $cover->path) }}" alt="{{ $product->name }}">
                        @else
                        <div class="no-img">
                            <i class="fa fa-cube"></i>
                        </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <h6>{{ $product->name }}</h6>
                        @if($product->categories->isNotEmpty())
                        <div class="category">{{ $product->categories->pluck('name')->implode(', ') }}</div>
                        @endif
                        <div class="price mt-2">R$ {{ number_format($product->price, 2, ',', '.') }}</div>
                    </div>
                </div>
            </a>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <p style="color:#E6E6E6;opacity:0.5;">Nenhum produto disponível no momento.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
