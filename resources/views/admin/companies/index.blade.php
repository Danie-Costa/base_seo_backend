@extends('layouts.app')

@section('content')

<div class="mt-3 pt-3 container">
    @include('admin.partials.searchbar', ['title' => 'Empresas', 'route' => 'admin.companies', 'model' => 'companies'])

    <div class="row">
        <div class="col-12 col-sm-1 text-center hide-mobile">#</div>
        <div class="col-6 col-sm-3 text-center"><p>Nome</p></div>
        <div class="col-6 col-sm-2 text-center"><p>CNPJ</p></div>
        <div class="col-12 col-sm-3 text-center hide-mobile"><p>Email</p></div>
        <div class="col-12 col-sm-1 text-center hide-mobile"><p>Usuarios</p></div>
        <div class="col-6 col-sm-2 text-center"></div>

        <div class="col-12 p-0">
            <hr class="p-0 m-1">
        </div>

        @foreach($companies as $item)
            <div class="col-12 col-sm-1 text-center hide-mobile">
                {{ $loop->iteration }}
            </div>

            <div class="col-6 col-sm-3 text-center">
                {{ $item->name }}
            </div>

            <div class="col-6 col-sm-2 text-center">
                {{ $item->cnpj }}
            </div>

            <div class="col-12 col-sm-3 text-center hide-mobile">
                {{ $item->primary_email }}
            </div>

            <div class="col-12 col-sm-1 text-center hide-mobile">
                {{ $item->users_count ?? 0 }}
            </div>

            <div class="col-6 col-sm-2 d-flex justify-content-center">
                {{ Form::open(['route' => ['admin.companies.destroy', $item->id], 'class' => 'confirmDelete']) }}
                <div class="btn-group btn-group-sm">
                    @include('admin.partials.crude-menu', [
                        'routePermission' => 'admin.companies',
                        'route' => 'admin.companies',
                        'data' => $item,
                        'model' => 'company',
                    ])
                </div>
                {{ Form::close() }}
            </div>

            <div class="col-12 p-0">
                <hr class="p-0 m-1">
            </div>
        @endforeach
    </div>

    <div class="mt-3">
        {{ $companies->links() }}
    </div>
</div>

@endsection
