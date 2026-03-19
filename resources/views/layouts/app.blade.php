<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'Laravel') }}</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

@vite(['resources/css/app.css','resources/css/admin.css', 'resources/js/products.js'])
</head>
<body>

{{-- Navbar --}}
<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand">{{ $app_name }}</span>

        <span class="text-white">
            Welcome, {{ $current_user->name ?? 'Guest' }}
        </span>
    </div>
</nav>

{{-- Main Content --}}
<div class="container mt-4">
    @yield('content')
</div>

</body>
</html>
