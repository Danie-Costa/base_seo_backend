@extends('layouts.app')

@section('content')

<div class="mt-3 pt-3 container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Arquivos</h3>
        <a href="{{ route('company.files.create') }}" class="btn btn-primary btn-sm">Novo Arquivo</a>
    </div>

    <div class="row">
        <div class="col-12 col-sm-1 text-center hide-mobile">#</div>
        <div class="col-6 col-sm-4 text-center"><p>Titulo</p></div>
        <div class="col-6 col-sm-5 text-center hide-mobile"><p>Caminho</p></div>
        <div class="col-6 col-sm-2 text-center"></div>
        <div class="col-12 p-0"><hr class="p-0 m-1"></div>
        @forelse($files as $item)
            <div class="col-12 col-sm-1 text-center hide-mobile">{{ $loop->iteration }}</div>
            <div class="col-6 col-sm-4 text-center">{{ $item->title ?? '-' }}</div>
            <div class="col-6 col-sm-5 text-center hide-mobile">{{ $item->path }}</div>
            <div class="col-6 col-sm-2 d-flex justify-content-center">
                {{ Form::open(['route' => ['company.files.destroy', $item->id], 'method' => 'delete']) }}
                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir?')">Excluir</button>
                {{ Form::close() }}
            </div>
            <div class="col-12 p-0"><hr class="p-0 m-1"></div>
        @empty
            <div class="col-12 text-center py-4 text-muted">Nenhum arquivo.</div>
        @endforelse
    </div>
    <div class="mt-3">{{ $files->links() }}</div>
</div>
@endsection
