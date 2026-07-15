<nav class="navbar navbar-expand-lg border-bottom">
    <div class="container">

        <a class="navbar-brand" href="{{ url('/') }}">
            Navbar
        </a>

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="Alternar navegação">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @foreach ($menu as $link => $item)
                    @php
                        $isExternal = isset($item['externallink']);
                        $href = $isExternal ? $item['externallink'] : route($link);
                        $active = !$isExternal && request()->routeIs($link);
                        $event = 'click_' . \Illuminate\Support\Str::slug($item['title']);
                    @endphp

                    <li class="nav-item">
                        <a class="nav-link {{ $active ? 'active' : '' }}"
                           href="{{ $href }}"
                           title="{{ $item['title'] }}"
                           @if(!$isExternal)
                               onclick="trackEvent('{{ $event }}', { origin: 'botao' })"
                           @endif
                        >
                            {{ $item['title'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <ul class="navbar-nav ms-auto">

                @guest

                    @if (Route::has('google.login'))
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-primary me-2"
                               href="{{ route('google.login') }}">
                                Cadastro
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('login') }}"
                           title="Login">
                            Login
                        </a>
                    </li>

                @else

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle"
                           href="#"
                           id="navbarDropdown"
                           role="button"
                           data-bs-toggle="dropdown"
                           aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end"
                            aria-labelledby="navbarDropdown">

                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Sair
                                </a>
                            </li>

                        </ul>

                        <form id="logout-form"
                              action="{{ route('logout') }}"
                              method="POST"
                              class="d-none">
                            @csrf
                        </form>

                    </li>

                @endguest

            </ul>

        </div>
    </div>
</nav>