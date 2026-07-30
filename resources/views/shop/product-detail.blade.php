@extends('layouts.public')

@section('content')
<style>
body { background:#0B1D33; }
.detail-card { background:#142C4C; border:1px solid rgba(255,255,255,0.06); border-radius:0.75rem; padding:2rem; }
.detail-card h3 { color:#fff; font-weight:700; }
.detail-card .price { color:#D4A017; font-size:1.8rem; font-weight:700; }
.detail-card .description { color:#E6E6E6; opacity:0.8; line-height:1.6; }
.detail-card .form-control { background:#0B1D33; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:0.5rem; padding:0.75rem 1rem; }
.detail-card .form-control:focus { border-color:#D4A017; box-shadow:0 0 0 3px rgba(212,160,23,0.08); }
.detail-card .btn-primary { background:#D4A017; border:none; color:#0B1D33; font-weight:700; padding:0.75rem 2rem; border-radius:0.5rem; }
.detail-card .btn-primary:hover { background:#e3b239; }
.detail-card .btn-outline-light { border-color:rgba(255,255,255,0.12); color:#E6E6E6; }
.detail-card .btn-outline-light:hover { border-color:#D4A017; color:#D4A017; }
.img-main { width:100%; max-height:400px; object-fit:contain; border-radius:0.5rem; background:#0B1D33; }
.img-thumb { width:60px; height:60px; object-fit:cover; border-radius:0.4rem; cursor:pointer; border:2px solid transparent; opacity:0.6; transition:all .2s; }
.img-thumb:hover, .img-thumb.active { border-color:#D4A017; opacity:1; }
</style>

<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="background:none;padding:0;margin:0;">
            <li class="breadcrumb-item"><a href="{{ route('shop.products') }}" style="color:#D4A017;text-decoration:none;font-size:0.85rem;">Produtos</a></li>
            <li class="breadcrumb-item active" style="color:#E6E6E6;opacity:0.5;font-size:0.85rem;">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="detail-card">
        <div class="row g-4">
            {{-- Imagens --}}
            <div class="col-md-6">
                @php $cover = $product->images->firstWhere('is_cover', true) ?? $product->images->first(); @endphp
                <img src="{{ $cover ? asset('storage/' . $cover->path) : 'https://via.placeholder.com/400?text=Sem+imagem' }}"
                     id="mainImage" class="img-main mb-2" alt="{{ $product->name }}">

                @if($product->images->count() > 1)
                <div class="d-flex gap-2 flex-wrap">
                    @foreach($product->images as $img)
                    <img src="{{ asset('storage/' . $img->path) }}"
                         class="img-thumb {{ $img->is_cover || $loop->first ? 'active' : '' }}"
                         onclick="document.getElementById('mainImage').src=this.src;document.querySelectorAll('.img-thumb').forEach(t=>t.classList.remove('active'));this.classList.add('active');">
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="col-md-6 d-flex flex-column">
                <h3>{{ $product->name }}</h3>

                @if($product->categories->isNotEmpty())
                <div class="mb-2">
                    @foreach($product->categories as $cat)
                    <span class="badge me-1" style="background:rgba(212,160,23,0.15);color:#D4A017;font-size:0.75rem;">{{ $cat->name }}</span>
                    @endforeach
                </div>
                @endif

                <div class="price mb-3">R$ {{ number_format($product->price, 2, ',', '.') }}</div>

                @if($product->description)
                <div class="description mb-4">
                    {{ $product->description }}
                </div>
                @endif

                <div class="mt-auto">
                    @if(Auth::guard('client')->check())
                    <form method="POST" action="{{ route('shop.cart.add', $product) }}">
                        @csrf
                        <div class="d-flex gap-2 align-items-center">
                            <input type="number" name="qty" value="1" min="1" max="99" class="form-control" style="width:80px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-shopping-cart"></i> Adicionar ao carrinho
                            </button>
                        </div>
                    </form>
                    @else
                    <a href="{{ route('client.login') }}" class="btn btn-outline-light">
                        Faça login para comprar
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
