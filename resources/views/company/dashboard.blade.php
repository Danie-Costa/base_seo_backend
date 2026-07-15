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
                        <p class="text-secondary small mb-0">Meus Produtos</p>
                        <h5 class="mb-0">{{ \App\Models\Product::where('company_id', auth()->user()->company_id)->count() }}</h5>
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
                        <p class="text-secondary small mb-0">Meus Posts</p>
                        <h5 class="mb-0">{{ \App\Models\Post::where('company_id', auth()->user()->company_id)->count() }}</h5>
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
                        <p class="text-secondary small mb-0">Clientes</p>
                        <h5 class="mb-0">{{ \App\Models\Client::where('company_id', auth()->user()->company_id)->count() }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
