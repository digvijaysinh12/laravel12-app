@props([
    'mode' => 'admin',
    'title' => null,
    'cartCount' => 0,
])

@php
    $user = auth()->user();
    $isAdmin = $mode === 'admin';
@endphp

<header class="sticky top-0 z-30 border-b border-slate-200 bg-white">
    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex min-w-0 items-center gap-3">
            @if ($isAdmin)
                <button
                    type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-600 hover:bg-slate-100 lg:hidden"
                    @click="sidebarOpen = true"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" />
                    </svg>
                </button>
            @endif

            <h1 class="truncate text-lg font-semibold text-slate-900">
                {{ $title ?: ($isAdmin ? 'Dashboard' : config('app.name')) }}
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <button
                type="button"
                class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-slate-200 text-slate-500 hover:bg-slate-100"
                title="Notifications"
            >
                <span class="sr-only">Notifications</span>
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M15 17h5l-1.4-1.4a2 2 0 0 1-.6-1.4v-3.2a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10 17a2 2 0 0 0 4 0" stroke-linecap="round"/>
                </svg>
            </button>

            @auth
                <div class="hidden text-right sm:block">
                    <div class="text-sm font-medium text-slate-900">{{ $user->name }}</div>
                    <div class="text-xs text-slate-500">{{ ucfirst($user->role) }}</div>
                </div>

                @if (! $isAdmin)
                    <a href="{{ route('user.cart.index') }}" class="hidden rounded-md border border-slate-200 bg-slate-50 px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-100 sm:inline-block">
                        Cart {{ $cartCount }}
                    </a>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </div>
</header>
