@php
    $user = auth()->user();
    $cartCount = collect(session('cart', []))->sum('quantity');
    $isUser = $user?->role === 'user';
    $dashboardRoute = $user?->role === 'admin' ? route('admin.dashboard') : route('user.dashboard');
@endphp

<header x-data="{ open: false, userOpen: false }" class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center gap-4">
            <a href="{{ route('home') }}" class="shrink-0 text-lg font-semibold tracking-tight text-slate-900">
                {{ config('app.name') }}
            </a>

            <form
                method="GET"
                action="{{ $isUser ? route('user.products.index') : route('login') }}"
                class="hidden flex-1 md:block"
            >
                <label for="global-search" class="sr-only">Search products</label>
                <div class="relative">
                    <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                    <input
                        id="global-search"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search for products, brands and more"
                        class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-200"
                    >
                </div>
            </form>

            <div class="ml-auto hidden items-center gap-2 md:flex">
                @auth
                    <a
                        href="{{ $isUser ? route('user.cart.index') : route('admin.orders.index') }}"
                        class="relative inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                    >
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="20" r="1"></circle>
                            <circle cx="17" cy="20" r="1"></circle>
                            <path d="M3 4h2l2.4 11h10.9l2-8H6.1"></path>
                        </svg>
                        <span>Cart</span>
                        @if ($cartCount > 0)
                            <span class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-slate-900 px-1.5 text-xs font-semibold text-white">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <div class="relative">
                        <button
                            type="button"
                            @click="userOpen = !userOpen"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                        >
                            <span>{{ $user->name }}</span>
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg>
                        </button>

                        <div
                            x-show="userOpen"
                            x-cloak
                            @click.outside="userOpen = false"
                            class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white p-1 shadow-lg"
                        >
                            <a href="{{ $dashboardRoute }}" class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100">Dashboard</a>
                            @if ($isUser)
                                <a href="{{ route('user.products.index') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100">Products</a>
                                <a href="{{ route('user.profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100">My Account</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-rose-600 hover:bg-rose-50">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a
                        href="{{ route('login') }}"
                        class="relative inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                    >
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="20" r="1"></circle>
                            <circle cx="17" cy="20" r="1"></circle>
                            <path d="M3 4h2l2.4 11h10.9l2-8H6.1"></path>
                        </svg>
                        <span>Cart</span>
                        @if ($cartCount > 0)
                            <span class="absolute -right-1 -top-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-slate-900 px-1.5 text-xs font-semibold text-white">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Login</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800">Register</a>
                @endauth
            </div>

            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-700 md:hidden"
                @click="open = !open"
                aria-label="Toggle menu"
            >
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"></path>
                </svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-cloak class="border-t border-slate-200 bg-white md:hidden">
        <div class="mx-auto w-full max-w-7xl space-y-3 px-4 py-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ $isUser ? route('user.products.index') : route('login') }}">
                <label for="mobile-search" class="sr-only">Search products</label>
                <input
                    id="mobile-search"
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search products"
                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-slate-300 focus:bg-white focus:ring-2 focus:ring-slate-200"
                >
            </form>

            @auth
                <a href="{{ $dashboardRoute }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Dashboard</a>
                @if ($isUser)
                    <a href="{{ route('user.products.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Products</a>
                    <a href="{{ route('user.cart.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Cart ({{ $cartCount }})</a>
                    <a href="{{ route('user.profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">My Account</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-lg border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-medium text-rose-700">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Cart</a>
                <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Login</a>
                <a href="{{ route('register') }}" class="block rounded-lg bg-slate-900 px-3 py-2 text-center text-sm font-medium text-white">Register</a>
            @endauth
        </div>
    </div>
</header>
