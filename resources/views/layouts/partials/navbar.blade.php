<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="#">Navbar</a>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                @foreach ($menu as $link => $item)
                    @php
                        $isExternal = isset($item['externallink']);
                        $href = $isExternal ? $item['externallink'] : route($link);
                        $active = !$isExternal && request()->routeIs($link);
                        $event = 'click_' . \Illuminate\Support\Str::slug($item['title']);
                    @endphp

                    <li class="nav-item">
                        <a
                            class="nav-link {{ $active ? 'active' : '' }}"
                            href="{{ $href }}"
                            title="{{ $item['title'] }}"
                            onclick="trackEvent('{{ $event }}', { origin: 'botao' })"
                        >
                            {{ $item['title'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
             <ul class="navbar-nav ms-auto">
                <!-- Authentication Links -->
                @guest
                    @if (Route::has('google.login'))
                        <li class="nav-item">
                            <a class="nav-link btn-blue-o" href="{{ route('google.login') }}">{{ __('Cadastro') }}</a>
                        </li>
                     @endif
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}"  title="Login">
                            Login
                        </a>
                    </li>
                
                @else
                
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="{{ route('logout') }}"
                            class="dropdown-item"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Sair
                            </a>

                            <form id="logout-form"
                                action="{{ route('logout') }}"
                                method="POST"
                                class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>