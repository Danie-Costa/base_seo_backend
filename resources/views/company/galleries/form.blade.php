@extends('layouts.app')

@section('content')

<div class="mt-3 pt-3 container">
    <h3>Nova Galeria</h3>
    {{ Form::open(['route' => 'company.galleries.store']) }}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Titulo</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}">
            </div>
        </div>
    </div>
    <div class="mt-3 text-center">
        <button type="submit" class="btn btn-success w-50">Criar</button>
    </div>
    {{ Form::close() }}
</div>
@endsection
