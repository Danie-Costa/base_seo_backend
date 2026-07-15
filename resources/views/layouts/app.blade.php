<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="{{ asset('bootstrap-5/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @stack('css')
</head>
<body>
    <div class="app-layout">
        @auth
        @include('layouts.partials.navbar')
        @endauth

        <main class="main-content @auth with-sidebar @endauth">
            @auth
            <div class="top-bar d-flex align-items-center justify-content-between px-4 py-2">
                <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarCanvas">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <span class="text-secondary small">{{ Auth::user()->name ?? '' }}</span>
                    <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-danger"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out"></i> Sair
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </div>
            </div>
            @endauth

            @yield('content')
        </main>
    </div>

    @stack('js')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="{{ asset('bootstrap-5/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/base.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
</body>
</html>
