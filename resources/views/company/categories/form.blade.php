@php $isEdit = !empty($data); $readonly = $readonly ?? false; @endphp

@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')

<div class="mt-3 pt-3 container">
    <h3>{{ $readonly ? 'Ver Categoria' : ($isEdit ? 'Editar Categoria' : 'Nova Categoria') }}</h3>

    {{ Form::open(['route' => $isEdit ? ['company.categories.update', $data->id] : 'company.categories.store', 'method' => $isEdit ? 'put' : 'post']) }}

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $data->name ?? '') }}" {{ $readonly ? 'disabled' : '' }} required>
            </div>

            <div class="mb-3">
                <label class="form-label">Categoria pai</label>
                <select name="parent_id" class="form-select" {{ $readonly ? 'disabled' : '' }}>
                    <option value="">Nenhuma (raiz)</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}" {{ old('parent_id', $data->parent_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @foreach($p->children as $child)
                            <option value="{{ $child->id }}" {{ old('parent_id', $data->parent_id ?? '') == $child->id ? 'selected' : '' }}>— {{ $child->name }}</option>
                        @endforeach
                    @endforeach
                </select>
                <small class="text-muted">Máximo 3 níveis</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo</label>
                <select name="type" class="form-select" {{ $readonly ? 'disabled' : '' }}>
                    <option value="product" {{ old('type', $type ?? $data->type ?? 'product') === 'product' ? 'selected' : '' }}>Produto</option>
                    <option value="post" {{ old('type', $type ?? $data->type ?? 'product') === 'post' ? 'selected' : '' }}>Blog</option>
                </select>
            </div>
        </div>
    </div>

    @if(!$readonly)
    <div class="mt-3 text-center">
        <button type="submit" class="btn btn-success w-50">Salvar</button>
    </div>
    @endif
    {{ Form::close() }}

    @if($isEdit && $data->children->isNotEmpty())
    <div class="card shadow-sm mt-4">
        <div class="card-header d-flex justify-content-between">
            <strong>Subcategorias ({{ $data->children->count() }})</strong>
            @if($data->canHaveChildren())
                <a href="{{ route('company.categories.create', ['type' => $data->type, 'parent_id' => $data->id]) }}" class="btn btn-sm btn-primary">Nova Subcategoria</a>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-1 text-center hide-mobile">#</div>
                <div class="col-6 col-sm-3"><p>Nome</p></div>
                <div class="col-6 col-sm-2 text-center hide-mobile"><p>Nível</p></div>
                <div class="col-6 col-sm-4 text-center"></div>
                <div class="col-12 p-0"><hr class="p-0 m-1"></div>
                @foreach($data->children as $child)
                <div class="col-12 col-sm-1 text-center hide-mobile">{{ $loop->iteration }}</div>
                <div class="col-6 col-sm-3">{{ $child->name }}</div>
                <div class="col-6 col-sm-2 text-center hide-mobile">Nível {{ $child->level }}</div>
                <div class="col-6 col-sm-4 d-flex justify-content-center gap-1">
                    <a href="{{ route('company.categories.show', $child->id) }}" class="btn btn-sm btn-info">Ver</a>
                    <a href="{{ route('company.categories.edit', $child->id) }}" class="btn btn-sm btn-primary">Editar</a>
                    {{ Form::open(['route' => ['company.categories.destroy', $child->id], 'class' => 'd-inline']) }}
                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Excluir?')">Excluir</button>
                    {{ Form::close() }}
                    {{ Form::open(['route' => ['company.categories.update', $child->id], 'method' => 'put', 'class' => 'd-inline']) }}
                    <input type="hidden" name="name" value="{{ $child->name }}">
                    <input type="hidden" name="type" value="{{ $child->type }}">
                    <input type="hidden" name="parent_id" value="">
                    <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Desvincular?')">Desvincular</button>
                    {{ Form::close() }}
                </div>
                <div class="col-12 p-0"><hr class="p-0 m-1"></div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
