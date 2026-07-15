<nav class="sidebar">
    <div class="sidebar-brand">
        <a href="{{ url('/') }}" class="text-decoration-none">
            <i class="fa fa-diamond"></i>
            <span>Atlas Labs</span>
        </a>
    </div>

    <ul class="sidebar-nav">
        @foreach ($menu as $link => $item)
            @php
                $active = request()->routeIs($link);
                $href = route($link);
            @endphp
            <li class="nav-item">
                <a class="nav-link {{ $active ? 'active' : '' }}" href="{{ $href }}">
                    <i class="fa {{ $item['icon'] ?? 'fa-circle-o' }}"></i>
                    <span>{{ $item['title'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="sidebar-footer">
        <small class="text-secondary">v1.0</small>
    </div>
</nav>

{{-- Offcanvas p/ mobile --}}
<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="sidebarCanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <ul class="sidebar-nav">
            @foreach ($menu as $link => $item)
                @php
                    $active = request()->routeIs($link);
                    $href = route($link);
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $active ? 'active' : '' }}" href="{{ $href }}">
                        <i class="fa {{ $item['icon'] ?? 'fa-circle-o' }}"></i>
                        <span>{{ $item['title'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
