@extends('layouts.app')

@section('content')
<div class="px-4 py-3">
    <h4 class="fw-semibold mb-3">Configuração de Pagamento</h4>

    @if(session('success'))
    <div class="alert alert-success" style="background:rgba(30,158,99,0.1);border:1px solid rgba(30,158,99,0.2);color:#1E9E63;padding:0.75rem;border-radius:0.5rem;margin-bottom:1rem;font-size:0.85rem;">{{ session('success') }}</div>
    @endif

    <div class="card border-0" style="background:#142C4C;">
        <div class="card-body">
            <p class="text-secondary small mb-3">Defina descontos por método de pagamento. Ex: 5% de desconto no PIX.</p>

            <form method="POST" action="{{ route('company.payment-config.update') }}">
                @csrf

                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Método</th>
                                <th>Desconto (%)</th>
                                <th>Ativo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $labels = ['pix'=>'PIX', 'credit_card'=>'Cartão de Crédito', 'debit_card'=>'Cartão de Débito']; @endphp
                            @foreach(['pix', 'credit_card', 'debit_card'] as $method)
                            <tr>
                                <td>{{ $labels[$method] ?? $method }}</td>
                                <td>
                                    <div class="input-group" style="max-width:150px;">
                                        <input type="number" name="discount_percent[{{ $method }}]"
                                               value="{{ $configs[$method]->discount_percent ?? 0 }}"
                                               class="form-control" min="0" max="100" step="0.01">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="active[{{ $method }}]" value="0">
                                        <input type="checkbox" name="active[{{ $method }}]" value="1"
                                               class="form-check-input" role="switch"
                                               id="active_{{ $method }}"
                                               {{ ($configs[$method]->active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="active_{{ $method }}"></label>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="btn btn-primary mt-3" style="background:#D4A017;border:none;color:#0B1D33;font-weight:600;">
                    Salvar configurações
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
