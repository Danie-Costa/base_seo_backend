@extends('layouts.public')

@section('content')
<style>
body { background:#0B1D33; }
.auth-card { background:#142C4C; border:1px solid rgba(255,255,255,0.06); border-radius:0.75rem; padding:2rem; }
.auth-card h4 { color:#fff; }
.auth-card .form-control { background:#0B1D33; border:1px solid rgba(255,255,255,0.08); color:#fff; border-radius:0.5rem; padding:0.75rem 1rem; }
.auth-card .form-control:focus { border-color:#D4A017; box-shadow:0 0 0 3px rgba(212,160,23,0.08); }
.auth-card .form-control::placeholder { color:#E6E6E6; opacity:0.4; }
.auth-card .btn-primary { background:#D4A017; border:none; color:#0B1D33; font-weight:700; padding:0.75rem; border-radius:0.5rem; width:100%; }
.auth-card .btn-primary:hover { background:#e3b239; }
.auth-card a { color:#D4A017; }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="auth-card">
                <h4 class="mb-1">Criar Conta</h4>
                <p class="text-secondary mb-4" style="opacity:0.7;font-size:0.9rem;">Cadastre-se para comprar</p>

                @if($errors->any())
                <div class="alert alert-danger" style="background:rgba(198,69,69,0.1);border:1px solid rgba(198,69,69,0.2);color:#C64545;padding:0.75rem;border-radius:0.5rem;margin-bottom:1rem;font-size:0.85rem;">
                    <ul style="margin:0;padding-left:1rem;">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('client.register') }}">
                    @csrf
                    <input type="text" name="name" class="form-control mb-3" placeholder="Nome completo" value="{{ old('name') }}" required autofocus>
                    <input type="email" name="email" class="form-control mb-3" placeholder="Email" value="{{ old('email') }}" required>
                    <input type="text" name="cnpj" class="form-control mb-3" placeholder="CPF/CNPJ" value="{{ old('cnpj') }}" required>
                    <input type="password" name="password" class="form-control mb-3" placeholder="Senha" required>
                    <input type="password" name="password_confirmation" class="form-control mb-3" placeholder="Confirmar senha" required>
                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                </form>

                <div class="text-center mt-3">
                    <span style="color:#E6E6E6;opacity:0.6;font-size:0.85rem;">Já tem conta?</span>
                    <a href="{{ route('client.login') }}" style="font-size:0.85rem;">Entrar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
