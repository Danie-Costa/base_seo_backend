@extends('layouts.app')

@section('content')
<div class="px-4 py-3">
    <h4 class="mb-4 fw-semibold">Dashboard</h4>

    {{-- Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary small mb-1">Volume Total</p>
                    <h4 class="mb-0" style="color:#D4A017;">R$ {{ number_format($volumeTotal, 2, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary small mb-1">Pedidos (pagos)</p>
                    <h4 class="mb-0" style="color:#1E9E63;">{{ $paidOrders }} <small class="text-secondary" style="font-size:0.7rem;">/ {{ $totalOrders }} total</small></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary small mb-1">Pagamentos Pendentes</p>
                    <h4 class="mb-0" style="color:#D4A017;">{{ $pendingCount }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 h-100">
                <div class="card-body">
                    <p class="text-secondary small mb-1">Falhas</p>
                    <h4 class="mb-0" style="color:#C64545;">{{ $failedCount }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Top produtos --}}
        <div class="col-md-6">
            <div class="card border-0 h-100">
                <div class="card-header border-0" style="background:transparent;">
                    <h6 class="fw-semibold mb-0">Produtos mais vendidos</h6>
                </div>
                <div class="card-body p-0">
                    @if($topProducts->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-end">Receita</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $p)
                                <tr>
                                    <td>{{ $p->name }}</td>
                                    <td class="text-center">{{ $p->total_qtd }}</td>
                                    <td class="text-end" style="color:#D4A017;">R$ {{ number_format($p->total_revenue, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-4">Nenhuma venda ainda.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Receita mensal --}}
        <div class="col-md-6">
            <div class="card border-0 h-100">
                <div class="card-header border-0" style="background:transparent;">
                    <h6 class="fw-semibold mb-0">Receita por mês</h6>
                </div>
                <div class="card-body p-0">
                    @if($revenueByMonth->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Mês</th>
                                    <th class="text-end">Receita</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($revenueByMonth as $r)
                                <tr>
                                    <td>{{ $r->month }}</td>
                                    <td class="text-end" style="color:#D4A017;">R$ {{ number_format($r->total, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-4">Nenhuma receita registrada.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Pendentes --}}
    <div class="card border-0 mt-4">
        <div class="card-header border-0" style="background:transparent;">
            <h6 class="fw-semibold mb-0">Pagamentos Pendentes</h6>
        </div>
        <div class="card-body p-0">
            @if($pendingPayments->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Título</th>
                            <th>Valor</th>
                            <th>Método</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingPayments as $pay)
                        <tr>
                            <td>{{ $pay->id }}</td>
                            <td>{{ $pay->title }}</td>
                            <td>R$ {{ number_format($pay->price, 2, ',', '.') }}</td>
                            <td>{{ $pay->payment_type ?? '-' }}</td>
                            <td>{{ $pay->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center text-muted py-4">Nenhum pagamento pendente.</div>
            @endif
        </div>
    </div>

    {{-- Legacy cards (counts) --}}
    <div class="row g-3 mt-3">
        <div class="col-md-3">
            <div class="card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3" style="background:rgba(212,160,23,0.1)">
                        <i class="fa fa-building fa-2x" style="color:var(--bs-primary)"></i>
                    </div>
                    <div>
                        <p class="text-secondary small mb-0">Empresas</p>
                        <h5 class="mb-0">{{ \App\Models\Company::count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3" style="background:rgba(30,158,99,0.1)">
                        <i class="fa fa-users fa-2x" style="color:var(--bs-success)"></i>
                    </div>
                    <div>
                        <p class="text-secondary small mb-0">Usuários</p>
                        <h5 class="mb-0">{{ \App\Models\User::count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3" style="background:rgba(41,128,185,0.1)">
                        <i class="fa fa-newspaper-o fa-2x" style="color:var(--bs-info)"></i>
                    </div>
                    <div>
                        <p class="text-secondary small mb-0">Posts</p>
                        <h5 class="mb-0">{{ \App\Models\Post::count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3" style="background:rgba(198,69,69,0.1)">
                        <i class="fa fa-tag fa-2x" style="color:var(--bs-danger)"></i>
                    </div>
                    <div>
                        <p class="text-secondary small mb-0">Produtos</p>
                        <h5 class="mb-0">{{ \App\Models\Product::count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
