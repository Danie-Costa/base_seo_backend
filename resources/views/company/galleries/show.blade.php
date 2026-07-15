@extends('layouts.app')

@section('content')

<div class="mt-3 pt-3 container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ $gallery->title ?? 'Galeria' }}</h3>
        <a href="{{ route('company.galleries.index') }}" class="btn btn-sm btn-secondary">Voltar</a>
    </div>

    {{ Form::open(['route' => ['company.galleries.upload', $gallery->id]]) }}
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex gap-2">
            <input type="text" name="title" class="form-control" placeholder="Titulo da imagem">
            <input type="text" name="path" class="form-control" placeholder="URL da imagem" required>
            <button type="submit" class="btn btn-primary">Adicionar</button>
        </div>
    </div>
    {{ Form::close() }}

    <div class="row">
        @forelse($gallery->images as $image)
            <div class="col-md-3 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <p class="mb-1"><strong>{{ $image->title ?? 'Imagem' }}</strong></p>
                        <p class="text-muted small">{{ $image->path }}</p>
                        {{ Form::open(['route' => ['company.galleries.destroyImage', $image->id], 'method' => 'delete']) }}
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir imagem?')">Excluir</button>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-4 text-muted">Nenhuma imagem nesta galeria.</div>
        @endforelse
    </div>
</div>
@endsection
