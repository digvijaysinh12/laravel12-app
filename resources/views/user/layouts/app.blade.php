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
    <title>{{ config('app.name') }}@hasSection('title') - @yield('title')@endif</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="text-lg font-semibold tracking-tight text-slate-900">
                {{ config('app.name') }}
            </a>
            <x-user.navbar />
        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="space-y-3">
            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('status'))
                <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                    {{ session('status') }}
                </div>
            @endif
        </div>

        <div class="pt-6">
            @yield('content')
        </div>
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto w-full max-w-7xl px-4 py-6 text-sm text-slate-500 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                <p>Simple storefront layout.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
