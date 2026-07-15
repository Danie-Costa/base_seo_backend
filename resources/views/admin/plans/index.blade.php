@extends('layouts.app')

@section('content')

<div class="mt-3 pt-3 container">
    @include('admin.partials.searchbar', ['title' => 'Planos', 'route' => 'admin.plans', 'model' => 'plans'])

    <div class="row">
        <div class="col-12 col-sm-1 text-center hide-mobile">#</div>
        <div class="col-6 col-sm-3 text-center"><p>Nome</p></div>
        <div class="col-6 col-sm-3 text-center hide-mobile"><p>Preco</p></div>
        <div class="col-6 col-sm-2 text-center"></div>

        <div class="col-12 p-0"><hr class="p-0 m-1"></div>

        @forelse($plans as $item)
            <div class="col-12 col-sm-1 text-center hide-mobile">{{ $loop->iteration }}</div>
            <div class="col-6 col-sm-3 text-center">{{ $item->name }}</div>
            <div class="col-6 col-sm-3 text-center hide-mobile">R$ {{ number_format($item->price, 2, ',', '.') }}</div>
            <div class="col-6 col-sm-2 d-flex justify-content-center">
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.plans.edit', $item->id) }}" class="btn btn-primary">Editar</a>
                    {{ Form::open(['route' => ['admin.plans.destroy', $item->id], 'class' => 'confirmDelete d-inline']) }}
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Excluir?')">Excluir</button>
                    {{ Form::close() }}
                </div>
            </div>
            <div class="col-12 p-0"><hr class="p-0 m-1"></div>
        @empty
            <div class="col-12 text-center py-4 text-muted">Nenhum plano encontrado.</div>
        @endforelse
    </div>

    <div class="mt-3">{{ $plans->links() }}</div>
</div>
@endsection
