@extends('layouts.app')

@section('content')

<div class="mt-3 pt-3 container">
    <h3>Novo Arquivo</h3>
    {{ Form::open(['route' => 'company.files.store']) }}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Titulo</label>
                <input type="text" name="title" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">URL do Arquivo</label>
                <input type="text" name="path" class="form-control" required>
            </div>
        </div>
    </div>
    <div class="mt-3 text-center">
        <button type="submit" class="btn btn-success w-50">Salvar</button>
    </div>
    {{ Form::close() }}
</div>
@endsection
