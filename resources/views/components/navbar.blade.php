@php
    $cartCount = count(session('cart', []));
    $dashboardRoute = route('user.dashboard');
@endphp

<header class="border-b border-slate-200 bg-white">
    <div class="mx-auto flex w-full max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="text-lg font-semibold tracking-tight text-slate-900">
                {{ config('app.name') }}
            </a>
        </div>

        <nav class="flex flex-wrap items-center gap-2 text-sm font-medium text-slate-600">
            <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 transition hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('home')) bg-slate-100 text-slate-900 @endif">Home</a>
            <a href="{{ route('user.products.index') }}" class="rounded-lg px-3 py-2 transition hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.products.*')) bg-slate-100 text-slate-900 @endif">Products</a>
            <a href="{{ route('user.cart.index') }}" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 transition hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.cart.*')) bg-slate-100 text-slate-900 @endif">
                <span>Cart</span>
                <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-slate-900 px-2 py-0.5 text-xs font-semibold text-white">
                    {{ $cartCount }}
                </span>
            </a>

            @auth
                <a href="{{ $dashboardRoute }}" class="rounded-lg px-3 py-2 transition hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.dashboard')) bg-slate-100 text-slate-900 @endif">Dashboard</a>
                <a href="{{ route('user.orders.index') }}" class="rounded-lg px-3 py-2 transition hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.orders.*')) bg-slate-100 text-slate-900 @endif">Orders</a>
                <a href="{{ route('user.profile.edit') }}" class="rounded-lg px-3 py-2 transition hover:bg-slate-100 hover:text-slate-900 @if(request()->routeIs('user.profile.*')) bg-slate-100 text-slate-900 @endif">Profile</a>
                <form method="POST" action="{{ route('logout') }}" class="inline-flex">
                    @csrf
                    <button type="submit" class="rounded-lg px-3 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 transition hover:bg-slate-100 hover:text-slate-900">Login</a>
                <a href="{{ route('register') }}" class="rounded-lg bg-slate-900 px-3 py-2 text-white transition hover:bg-slate-800">Register</a>
            @endauth
        </nav>
    </div>
</header>
