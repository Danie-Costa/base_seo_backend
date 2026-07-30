@extends('layouts.public')

@section('content')
<style>
body { background:#0B1D33; }
.result-card { background:#142C4C; border:1px solid rgba(255,255,255,0.06); border-radius:0.75rem; padding:2.5rem; max-width:500px; margin:0 auto; text-align:center; }
.result-card h4 { color:#fff; font-weight:700; }
.result-card .btn-primary { background:#D4A017; border:none; color:#0B1D33; font-weight:700; border-radius:0.5rem; padding:0.75rem 2rem; }
.result-card .btn-primary:hover { background:#e3b239; }
</style>

<div class="container py-5">
    <div class="result-card">
        <div style="font-size:3rem;color:#1E9E63;margin-bottom:1rem;">✅</div>
        <h4>Pagamento Confirmado!</h4>
        <p style="color:#E6E6E6;opacity:0.8;">Seu pagamento foi processado com sucesso.</p>
        @if($ref)
        <p style="color:#E6E6E6;opacity:0.5;font-size:0.8rem;">Ref: {{ $ref }}</p>
        @endif
        <a href="{{ route('shop.products') }}" class="btn btn-primary mt-3">Continuar comprando</a>
    </div>
</div>
@endsection
