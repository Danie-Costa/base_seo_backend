@extends('layouts.app')

@section('content')
<div class="px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-semibold mb-0">Leads</h4>
    </div>

    <div class="card border-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th class="hide-mobile">Telefone</th>
                        <th class="hide-mobile">Empresa</th>
                        <th class="hide-mobile">Data</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td class="hide-mobile">{{ $item->phone ?? '-' }}</td>
                        <td class="hide-mobile">{{ $item->company_name ?? '-' }}</td>
                        <td class="hide-mobile">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($item->message)
                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="popover" data-bs-content="{{ $item->message }}" title="Mensagem">Msg</button>
                            @endif
                            {{ Form::open(['route' => ['admin.leads.destroy', $item->id], 'method' => 'delete', 'class' => 'd-inline']) }}
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir lead?')">X</button>
                            {{ Form::close() }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Nenhum lead recebido.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $leads->links() }}</div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    popoverTriggerList.map(function (el) { return new bootstrap.Popover(el) })
});
</script>
@endpush
