@php $isEdit = !empty($data); @endphp

@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')

<div class="mt-3 pt-3 container">
    <h3>{{ $isEdit ? 'Editar Cliente' : 'Novo Cliente' }}</h3>
    {{ Form::open(['route' => $isEdit ? ['company.clients.update', $data->id] : 'company.clients.store', 'method' => $isEdit ? 'put' : 'post']) }}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $data->name ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $data->email ?? '') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">CNPJ</label>
                <input type="text" name="cnpj" class="form-control" value="{{ old('cnpj', $data->cnpj ?? '') }}" required maxlength="14">
            </div>
        </div>
    </div>
    <div class="mt-3 text-center">
        <button type="submit" class="btn btn-success w-50">Salvar</button>
    </div>
    {{ Form::close() }}
</div>
@endsection
