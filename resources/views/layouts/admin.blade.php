<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Admin | ' . config('app.name'))</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin.js'])
    </head>
    <body class="layout-admin">

        <x-sidebar />

        <div class="layout-admin__main">
            <header class="admin-topbar">
                <div class="admin-topbar__title">
                    @yield('page-title', 'Dashboard')
                </div>
                <div class="admin-topbar__actions">
                    <x-button href="{{ route('dashboard') }}" variant="ghost" class="btn-sm">View Site</x-button>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <x-button type="submit" variant="danger" class="btn-sm">Logout</x-button>
                    </form>
                </div>
            </header>

            <main class="layout-admin__content">
                @yield('content')
            </main>
        </div>

        @stack('scripts')
    </body>
</html>
