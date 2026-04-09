    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @auth
            <meta name="auth-user-id" content="{{ auth()->id() }}">
            <meta name="auth-user-role" content="{{ auth()->user()->role }}">
        @endauth
        <title>{{ config('app.name') }} - @yield('page-title', 'Admin')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <div class="min-h-screen lg:flex">
            <aside class="hidden w-72 shrink-0 border-r border-slate-200 bg-white lg:flex lg:flex-col">
                <div class="border-b border-slate-200 px-5 py-5">
                    <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold text-slate-900">
                        {{ config('app.name') }}
                    </a>
                    <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-500">Admin panel</p>
                </div>
                <x-admin.sidebar />
            </aside>

            <div class="min-w-0 flex-1">
                <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
                    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.18em] text-slate-500">Admin</p>
                            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">@yield('page-title', 'Dashboard')</h1>
                        </div>

                        <div class="flex items-center gap-3">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="space-y-4 p-4 sm:p-6 lg:p-8">
                    <x-admin.toast tone="success" :message="session('success')" />
                    <x-admin.toast tone="danger" :message="session('error')" />
                    <x-admin.toast tone="info" :message="session('status')" />
                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
    </html>
