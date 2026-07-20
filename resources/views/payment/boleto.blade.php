@extends('layouts.public')

@section('content')
<div class="container py-5 text-center">
    <div style="max-width:500px;margin:0 auto;background:#142C4C;border-radius:0.75rem;padding:2.5rem;border:1px solid rgba(255,255,255,0.06);">
        <div style="font-size:3rem;color:#D4A017;margin-bottom:1rem;">📄</div>
        <h4 style="color:#fff;font-weight:700;">Boleto Gerado</h4>
        <p style="color:#E6E6E6;opacity:0.8;">Seu boleto foi gerado com sucesso. Pague em qualquer banco ou lotérica.</p>
        <a href="{{ $ticket_url }}" target="_blank" class="btn btn-primary" style="background:#D4A017;border:none;color:#0B1D33;font-weight:700;padding:0.75rem 2rem;border-radius:0.5rem;">Visualizar Boleto</a>
        <p style="color:#E6E6E6;opacity:0.5;font-size:0.8rem;margin-top:1rem;">O pagamento será confirmado em até 3 dias úteis.</p>
    </div>
</div>
@endsection
