@extends('layouts.app')

@section('content')
<div class="px-4 py-3">
    <h4 class="mb-4 fw-semibold">Dashboard</h4>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3" style="background:rgba(212,160,23,0.1)">
                        <i class="fa fa-building fa-2x" style="color:var(--bs-primary)"></i>
                    </div>
                    <div>
                        <p class="text-secondary small mb-0">Empresas</p>
                        <h5 class="mb-0">{{ \App\Models\Company::count() }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3" style="background:rgba(30,158,99,0.1)">
                        <i class="fa fa-users fa-2x" style="color:var(--bs-success)"></i>
                    </div>
                    <div>
                        <p class="text-secondary small mb-0">Usuários</p>
                        <h5 class="mb-0">{{ \App\Models\User::count() }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3" style="background:rgba(41,128,185,0.1)">
                        <i class="fa fa-newspaper-o fa-2x" style="color:var(--bs-info)"></i>
                    </div>
                    <div>
                        <p class="text-secondary small mb-0">Posts</p>
                        <h5 class="mb-0">{{ \App\Models\Post::count() }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3" style="background:rgba(198,69,69,0.1)">
                        <i class="fa fa-tag fa-2x" style="color:var(--bs-danger)"></i>
                    </div>
                    <div>
                        <p class="text-secondary small mb-0">Produtos</p>
                        <h5 class="mb-0">{{ \App\Models\Product::count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
