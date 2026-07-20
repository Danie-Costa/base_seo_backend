@extends('layouts.public')

@section('content')
<div class="container py-5 text-center">
    <div style="max-width:500px;margin:0 auto;background:#142C4C;border-radius:0.75rem;padding:2.5rem;border:1px solid rgba(255,255,255,0.06);">
        <div style="font-size:3rem;color:#C64545;margin-bottom:1rem;">❌</div>
        <h4 style="color:#fff;font-weight:700;">Pagamento não aprovado</h4>
        <p style="color:#E6E6E6;opacity:0.8;">Tente novamente com outra forma de pagamento.</p>
        <a href="{{ route('payment.checkout', $payment->id) }}" class="btn btn-primary" style="background:#D4A017;border:none;color:#0B1D33;font-weight:700;padding:0.75rem 2rem;border-radius:0.5rem;">Tentar novamente</a>
    </div>
</div>
@endsection
