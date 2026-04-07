<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">
        <div class="w-full max-w-md">
            <a href="/" class="mb-6 block text-center text-lg font-semibold text-slate-900">
                {{ config('app.name') }}
            </a>

            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                {{ $slot }}
            </div>
        </div>
    </main>
</body>
</html>
