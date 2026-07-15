@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')

<div class="mt-3 pt-3 container">
    @include('admin.partials.searchbar', ['title' => 'Meus Posts', 'route' => 'company.posts', 'model' => 'posts'])

    <div class="row">
        <div class="col-12 col-sm-1 text-center hide-mobile">#</div>
        <div class="col-6 col-sm-5 text-center"><p>Titulo</p></div>
        <div class="col-12 col-sm-2 text-center hide-mobile"><p>Publicado em</p></div>
        <div class="col-6 col-sm-2 text-center"></div>
        <div class="col-12 p-0"><hr class="p-0 m-1"></div>
        @forelse($posts as $item)
            <div class="col-12 col-sm-1 text-center hide-mobile">{{ $loop->iteration }}</div>
            <div class="col-6 col-sm-5 text-center">{{ $item->title }}</div>
            <div class="col-12 col-sm-2 text-center hide-mobile">{{ $item->published_at?->format('d/m/Y') ?? '-' }}</div>
            <div class="col-6 col-sm-2 d-flex justify-content-center">
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('company.posts.edit', $item->id) }}" class="btn btn-primary">Editar</a>
                    {{ Form::open(['route' => ['company.posts.destroy', $item->id], 'class' => 'd-inline']) }}
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Excluir?')">Excluir</button>
                    {{ Form::close() }}
                </div>
            </div>
            <div class="col-12 p-0"><hr class="p-0 m-1"></div>
        @empty
            <div class="col-12 text-center py-4 text-muted">Nenhum post publicado.</div>
        @endforelse
    </div>
    <div class="mt-3">{{ $posts->links() }}</div>
</div>
@endsection
