@extends('layouts.app')

@section('content')
<div  class="mt-3 pt-3 container">
    <h3 class="py-2">{{$company->name}}</h3>
    {{ Form::open(['route' => ['company.mycompany.update',$company->id],'class' => '','method' => 'put', 'files' => true]) }}

    <div class="card shadow-sm">
        <div class="card-header">{{$company->name}}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <label class="form-label">Nome</label>
                    <input type="text"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $company->name ?? '') }}"
                        required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">CNPJ</label>
                    <input type="text"
                        name="cnpj"
                        class="form-control @error('cnpj') is-invalid @enderror"
                        value="{{ old('cnpj', $company->cnpj ?? '') }}"
                        required>
                    @error('cnpj')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Endereco</label>
                    <input type="text"
                        name="address"
                        class="form-control @error('address') is-invalid @enderror"
                        value="{{ old('address', $company->address ?? '') }}"
                        required>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Telefone principal</label>
                    <input type="text"
                        name="primary_phone"
                        class="form-control @error('primary_phone') is-invalid @enderror"
                        value="{{ old('primary_phone', $company->primary_phone ?? '') }}"
                        required>
                    @error('primary_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Email principal</label>
                    <input type="email"
                        name="primary_email"
                        class="form-control @error('primary_email') is-invalid @enderror"
                        value="{{ old('primary_email', $company->primary_email ?? '') }}"
                        required>
                    @error('primary_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Logo (URL)</label>
                    <input type="text"
                        name="logo"
                        class="form-control @error('logo') is-invalid @enderror"
                        value="{{ old('logo', $company->logo ?? '') }}">
                    @error('logo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if($company->logo)
                        <div class="mt-2">
                            <img src="{{ $company->logo }}" alt="Logo" style="max-height: 60px;" class="img-thumbnail">
                        </div>
                    @endif
                </div>
                <div class="col-12 col-md-12 pt-2 ">
                    <button type="submit" class="btn btn-success btn-sm w-50 ">Salvar</button>
                </div>   
            </div>
        </div>
    </div>
    

    {{ Form::close() }}
    @if($company)
        <div class="card shadow-sm mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Usuarios da empresa</strong>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#userCreate">
                    Adicionar usuario
                </button>
            </div>

            <div class="card-body">
                <div class="row">
                    @if($company->users->isEmpty())
                        <div class="col-12">
                            <p class="mb-0 text-muted">Nenhum usuario vinculado a esta empresa.</p>
                        </div>
                    @else    
                        @foreach($company->users as $user)
                            <div class="col-6 col-md-4">
                                <div class="card  shadow-sm">
                                    <div class="card-body">
                                        <p>{{ $user->name }}</p>
                                        <p>{{ $user->email }}</p>
                                        <p>{{ \App\Models\User::RULES[$user->rule] ?? '-' }}</p>
                                        <p class="text-right">
                                            <a href="{{ route('company.users.edit', ['id' => $user->id]) }}" class="btn btn-sm btn-primary">Editar</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
@include('company.users.create')
@endsection
@section('js')
    @stack('incjs')
@endsection
@section('css')
<style>
    .nav-desk {
        position: relative  !important;
    }
</style>
    @stack('inccss')
@endsection