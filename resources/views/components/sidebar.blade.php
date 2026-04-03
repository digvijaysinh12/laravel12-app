@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
        ['label' => 'Products', 'route' => 'admin.products.index'],
        ['label' => 'Orders', 'route' => 'admin.orders.index'],
        ['label' => 'Reports', 'route' => 'admin.reports.index'],
        ['label' => 'Users', 'route' => 'admin.users.index'],
    ];
@endphp

<aside class="sidebar" data-sidebar>
    <div class="sidebar__brand">
        <div class="sidebar__logo">{{ strtoupper(substr(config('app.name'), 0, 1)) }}</div>
        <div>
            <div class="sidebar__title">{{ config('app.name') }}</div>
            <div class="sidebar__muted">Admin Panel</div>
        </div>
    </div>

    <nav class="sidebar__nav">
        @foreach($navItems as $item)
            @if(Route::has($item['route']))
                <a href="{{ route($item['route']) }}"
                   class="sidebar__link {{ request()->routeIs($item['route'].'*') ? 'is-active' : '' }}">
                    <span>{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <div class="sidebar__footer">
        @auth
            <div class="text-muted text-sm">Signed in as</div>
            <div class="fw-bold">{{ auth()->user()->name }}</div>
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm btn-full">Logout</button>
            </form>
        @endauth
    </div>
</aside>
