@extends('layouts.app')

@section('content')

<div class="px-4 py-5 text-center">
    <div style="max-width:500px;margin:0 auto;background:#142C4C;border-radius:0.75rem;padding:2.5rem;border:1px solid rgba(255,255,255,0.06);">
        <div style="font-size:3rem;color:#1E9E63;margin-bottom:1rem;">✅</div>
        <h4 style="color:#fff;font-weight:700;">Plano Contratado!</h4>
        <p style="color:#E6E6E6;opacity:0.8;">Seu plano <strong>{{ $plan->name }}</strong> está ativo.</p>
        <a href="{{ route('company.plans.index') }}" class="btn btn-primary mt-3" style="background:#D4A017;border:none;color:#0B1D33;font-weight:700;">Ver meus planos</a>
    </div>
</div>
@endsection
