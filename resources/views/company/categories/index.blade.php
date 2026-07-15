@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')

<div class="mt-3 pt-3 container">
    <div class="d-flex justify-content-between mb-3">
        <div>
            <a href="{{ route('company.categories.index', ['type' => 'product']) }}" class="btn btn-sm {{ $type === 'product' ? 'btn-primary' : 'btn-outline-primary' }}">Produtos</a>
            <a href="{{ route('company.categories.index', ['type' => 'post']) }}" class="btn btn-sm {{ $type === 'post' ? 'btn-primary' : 'btn-outline-primary' }}">Blog</a>
        </div>
        <a href="{{ route('company.categories.create', ['type' => $type]) }}" class="btn btn-primary btn-sm">Nova Categoria</a>
    </div>

    <div class="row">
        <div class="col-12 col-sm-1 text-center hide-mobile">#</div>
        <div class="col-6 col-sm-5 text-center"><p>Nome</p></div>
        <div class="col-6 col-sm-2 text-center hide-mobile"><p>Tipo</p></div>
        <div class="col-6 col-sm-2 text-center"></div>
        <div class="col-12 p-0"><hr class="p-0 m-1"></div>
        @forelse($categories as $item)
            <div class="col-12 col-sm-1 text-center hide-mobile">{{ $loop->iteration }}</div>
            <div class="col-6 col-sm-5 text-center">{{ $item->name }}</div>
            <div class="col-6 col-sm-2 text-center hide-mobile">{{ $item->type }}</div>
            <div class="col-6 col-sm-2 d-flex justify-content-center">
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('company.categories.edit', $item->id) }}" class="btn btn-primary">Editar</a>
                    {{ Form::open(['route' => ['company.categories.destroy', $item->id], 'class' => 'd-inline']) }}
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Excluir?')">Excluir</button>
                    {{ Form::close() }}
                </div>
            </div>
            <div class="col-12 p-0"><hr class="p-0 m-1"></div>
        @empty
            <div class="col-12 text-center py-4 text-muted">Nenhuma categoria.</div>
        @endforelse
    </div>
    <div class="mt-3">{{ $categories->links() }}</div>
</div>
@endsection
