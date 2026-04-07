@php
    $user = auth()->user();
    $cartCount = collect(session('cart', []))->sum('quantity');
@endphp

<header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-slate-200 bg-white">
    <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="{{ route('user.dashboard') }}" class="text-base font-semibold tracking-tight text-slate-900">
            {{ config('app.name') }}
        </a>

        <nav class="hidden items-center gap-2 md:flex">
            <a href="{{ route('user.products.index') }}"
                class="rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('user.products.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                Products
            </a>

            <a href="{{ route('user.cart.index') }}"
                class="rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('user.cart.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                Cart
                @if ($cartCount > 0)
                    <span class="ml-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-slate-100 px-1.5 text-xs font-semibold text-slate-700">
                        {{ $cartCount }}
                    </span>
                @endif
            </a>

            <a href="{{ route('user.profile.edit') }}"
                class="rounded-md px-3 py-2 text-sm font-medium transition {{ request()->routeIs('user.profile.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }}">
                Profile
            </a>
        </nav>

        <div class="hidden items-center gap-3 md:flex">
            <div class="text-right">
                <p class="text-sm font-medium text-slate-900">{{ $user?->name }}</p>
                <p class="text-xs text-slate-500">{{ $user?->email }}</p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800">
                    Logout
                </button>
            </form>
        </div>

        <button type="button"
            class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-slate-200 text-slate-700 md:hidden"
            @click="open = !open"
            aria-label="Toggle menu">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" />
            </svg>
        </button>
    </div>

    <div x-show="open" x-cloak class="border-t border-slate-200 bg-white md:hidden">
        <div class="mx-auto w-full max-w-7xl space-y-2 px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('user.products.index') }}"
                class="block rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('user.products.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                Products
            </a>

            <a href="{{ route('user.cart.index') }}"
                class="block rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('user.cart.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                Cart ({{ $cartCount }})
            </a>

            <a href="{{ route('user.profile.edit') }}"
                class="block rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('user.profile.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                Profile
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800">
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>
