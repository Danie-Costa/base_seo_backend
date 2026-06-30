@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')

<div class="mt-3 pt-3 container">
    @include('admin.partials.searchbar', ['title' => 'Usuarios', 'route' => 'admin.users', 'model' => 'users'])

    <div class="row mb-3">
        <div class="col-12 col-sm-4 ml-auto">
            {!! Form::open(['method' => 'get', 'class' => 'form-inline w-100']) !!}
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <select name="company_id" class="form-control w-100" onchange="this.form.submit()">
                    <option value="">Todas as empresas</option>

                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ (string) request('company_id') === (string) $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            {!! Form::close() !!}
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-1 text-center hide-mobile">#</div>
        <div class="col-6 col-sm-2 text-center"><p>Nome</p></div>
        <div class="col-6 col-sm-3 text-center"><p>Email</p></div>
        <div class="col-12 col-sm-3 text-center hide-mobile"><p>Empresa</p></div>
        <div class="col-12 col-sm-1 text-center hide-mobile"><p>Perfil</p></div>
        <div class="col-6 col-sm-2 text-center"></div>

        <div class="col-12 p-0">
            <hr class="p-0 m-1">
        </div>

        @foreach($users as $item)
            <div class="col-12 col-sm-1 text-center hide-mobile">
                {{ $loop->iteration }}
            </div>

            <div class="col-6 col-sm-2 text-center">
                {{ $item->name }}
            </div>

            <div class="col-6 col-sm-3 text-center">
                {{ $item->email }}
            </div>

            <div class="col-12 col-sm-3 text-center hide-mobile">
                {{ $item->company->name ?? '-' }}
            </div>

            <div class="col-12 col-sm-1 text-center hide-mobile">
                {{ \App\Models\User::RULES[$item->rule] ?? '-' }}
            </div>

            <div class="col-6 col-sm-2 d-flex justify-content-center">
                {{ Form::open(['route' => ['admin.users.destroy', $item->id], 'class' => 'confirmDelete']) }}
                <div class="btn-group btn-group-sm">
                    @include('admin.partials.crude-menu', [
                        'routePermission' => 'admin.users',
                        'route' => 'admin.users',
                        'data' => $item,
                        'model' => 'user',
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
        {{ $users->links() }}
    </div>
</div>

@endsection
