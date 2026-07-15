@extends('layouts.app')

@section('content')

<div class="mt-3 pt-3 container">
    @include('admin.partials.searchbar', ['title' => 'Meus Produtos', 'route' => 'company.products', 'model' => 'products'])

    <div class="row">
        <div class="col-12 col-sm-1 text-center hide-mobile">#</div>
        <div class="col-6 col-sm-3 text-center"><p>Nome</p></div>
        <div class="col-12 col-sm-2 text-center hide-mobile"><p>Preco</p></div>
        <div class="col-12 col-sm-1 text-center hide-mobile"><p>Ativo</p></div>
        <div class="col-6 col-sm-2 text-center"></div>
        <div class="col-12 p-0"><hr class="p-0 m-1"></div>
        @forelse($products as $item)
            <div class="col-12 col-sm-1 text-center hide-mobile">{{ $loop->iteration }}</div>
            <div class="col-6 col-sm-3 text-center">{{ $item->name }}</div>
            <div class="col-12 col-sm-2 text-center hide-mobile">R$ {{ number_format($item->price, 2, ',', '.') }}</div>
            <div class="col-12 col-sm-1 text-center hide-mobile">{{ $item->active ? 'Sim' : 'Nao' }}</div>
            <div class="col-6 col-sm-2 d-flex justify-content-center">
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('company.products.edit', $item->id) }}" class="btn btn-primary">Editar</a>
                    {{ Form::open(['route' => ['company.products.destroy', $item->id], 'class' => 'd-inline']) }}
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Excluir?')">Excluir</button>
                    {{ Form::close() }}
                </div>
            </div>
            <div class="col-12 p-0"><hr class="p-0 m-1"></div>
        @empty
            <div class="col-12 text-center py-4 text-muted">Nenhum produto cadastrado.</div>
        @endforelse
    </div>
    <div class="mt-3">{{ $products->links() }}</div>
</div>
@endsection
