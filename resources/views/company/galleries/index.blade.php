@extends('layouts.app')

@section('content')

<div class="mt-3 pt-3 container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Galerias</h3>
        <a href="{{ route('company.galleries.create') }}" class="btn btn-primary btn-sm">Nova Galeria</a>
    </div>

    <div class="row">
        @forelse($galleries as $item)
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>{{ $item->title ?? 'Sem titulo' }}</h5>
                        <p class="text-muted">{{ $item->images_count }} imagens</p>
                        <a href="{{ route('company.galleries.show', $item->id) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                        {{ Form::open(['route' => ['company.galleries.destroy', $item->id], 'class' => 'd-inline', 'method' => 'delete']) }}
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir galeria?')">Excluir</button>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-4 text-muted">Nenhuma galeria criada.</div>
        @endforelse
    </div>
    <div class="mt-3">{{ $galleries->links() }}</div>
</div>
@endsection
