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
    <title>{{ config('app.name') }}@hasSection('title') - @yield('title')@endif</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <x-navbar />

    <main class="mx-auto w-full max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="space-y-4">
            <x-alert type="success" :message="session('success')" />
            <x-alert type="error" :message="session('error')" />
            <x-alert type="info" :message="session('status')" />
        </div>

        <div class="pt-6">
            @yield('content')
        </div>
    </main>

    <x-footer />

    @stack('scripts')
</body>
</html>
