@extends('layouts.app')

@section('content')

<div class="px-4 py-3">
    <h4 class="fw-semibold mb-3">Minhas Ordens</h4>

    <div class="card border-0" style="background:#142C4C;">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Título</th>
                        <th>Valor</th>
                        <th class="hide-mobile">Forma</th>
                        <th>Status</th>
                        <th class="hide-mobile">Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->title }}</td>
                        <td>R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                        <td class="hide-mobile">{{ $item->payment_type ?? '-' }}</td>
                        <td>
                            @if($item->status === 'approved') <span style="color:#1E9E63;">Aprovado</span>
                            @elseif($item->status === 'pending') <span style="color:#D4A017;">Pendente</span>
                            @else <span style="color:#C64545;">Falha</span>
                            @endif
                        </td>
                        <td class="hide-mobile">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Nenhuma ordem encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $payments->links() }}</div>
</div>
@endsection
