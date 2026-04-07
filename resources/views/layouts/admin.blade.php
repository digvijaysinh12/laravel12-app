<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="auth-user-id" content="{{ auth()->id() }}">
        <meta name="auth-user-role" content="{{ auth()->user()->role }}">
    @endauth
    <title>{{ config('app.name') }} - @yield('page-title', 'Admin') </title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="layout-admin bg-slate-50 text-slate-800">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen" @keydown.escape.window="sidebarOpen = false">
        <div class="flex min-h-screen">
            <x-sidebar />

            <div class="min-w-0 flex-1 lg:ml-0">
                <x-topbar mode="admin" :title="trim($__env->yieldContent('page-title', 'Dashboard'))" />

                <main class="p-4 sm:p-6 lg:p-8">
                    @if (session('success') || session('error') || session('status'))
                        <div class="mb-6 space-y-3">
                            @if (session('success'))
                                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                                    {{ session('error') }}
                                </div>
                            @endif
                            @if (session('status'))
                                <div class="rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                                    {{ session('status') }}
                                </div>
                            @endif
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
