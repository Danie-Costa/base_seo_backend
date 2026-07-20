@extends('layouts.app')

@section('content')

<div class="px-4 py-3">
    <h4 class="fw-semibold mb-3">Planos</h4>

    @if(session('success'))
    <div class="alert alert-success" style="background:rgba(30,158,99,0.1);border:1px solid rgba(30,158,99,0.2);color:#1E9E63;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger" style="background:rgba(198,69,69,0.1);border:1px solid rgba(198,69,69,0.2);color:#C64545;">{{ session('error') }}</div>
    @endif

    @if($company->plan_status === 'active')
    <div class="card border-0 mb-4" style="background:#142C4C;">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <strong style="color:#D4A017;">Plano atual: {{ $company->plan->name ?? '-' }}</strong>
                <span class="text-secondary small ms-2">Válido até {{ $company->plan_expires_at?->format('d/m/Y') ?? '-' }}</span>
            </div>
            @if($company->plan_started_at && now()->diffInDays($company->plan_started_at) <= 7)
            <div class="d-flex gap-2">
                <a href="{{ route('company.plans.cancel') }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancelar plano? O reembolso será processado.')">Cancelar plano (7 dias)</a>
            </div>
            @endif
        </div>
    </div>
    @elseif($company->plan_status === 'canceled')
    <div class="card border-0 mb-4" style="background:#142C4C;">
        <div class="card-body">
            <strong style="color:#C64545;">Plano cancelado</strong>
            <span class="text-secondary small ms-2">Cancelado em {{ $company->plan_canceled_at?->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    @endif

    <div class="row g-4">
        @foreach($plans as $plan)
        <div class="col-md-4">
            <div class="card border-0 h-100" style="background:#142C4C;">
                <div class="card-body d-flex flex-column">
                    <h5 class="fw-bold mb-1">{{ $plan->name }}</h5>
                    <p class="text-secondary small flex-fill">{{ $plan->description }}</p>

                    <div class="mb-3">
                        <strong style="font-size:1.5rem;color:#D4A017;">R$ {{ number_format($plan->price, 2, ',', '.') }}</strong>
                        <span class="text-secondary small">/mês</span>
                    </div>

                    <div class="mb-3">
                        @foreach(['monthly', 'semiannual', 'annual'] as $int)
                            @php
                                $mult = match($int) { 'monthly'=>1, 'semiannual'=>6, 'annual'=>12 };
                                $disc = match($int) { 'monthly'=>1, 'semiannual'=>0.9, 'annual'=>0.8 };
                                $total = $plan->price * $mult * $disc;
                                $label = match($int) { 'monthly'=>'Mensal', 'semiannual'=>'Semestral', 'annual'=>'Anual' };
                                $isBest = $int === 'annual';
                            @endphp
                            <label class="d-flex align-items-center gap-2 py-1" style="cursor:pointer;">
                                <input type="radio" name="plan_{{ $plan->id }}" value="{{ $int }}" onchange="location='{{ route('company.plans.checkout', ['plan' => $plan->id, 'interval' => $int]) }}'">
                                <span class="small">{{ $label }}</span>
                                <span class="small text-secondary">R$ {{ number_format($total, 2, ',', '.') }}</span>
                                @if($isBest)<span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:0.6rem;">ECONOMIA</span>@endif
                            </label>
                        @endforeach
                    </div>

                    <a href="{{ route('company.plans.checkout', ['plan' => $plan->id, 'interval' => 'monthly']) }}" class="btn btn-primary w-100 mt-auto" style="background:#D4A017;border:none;color:#0B1D33;font-weight:600;">
                        Contratar
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
