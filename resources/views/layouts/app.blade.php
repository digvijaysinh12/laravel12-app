<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ config('app.name') }}</title>

        <meta name="csrf-token" content="{{ csrf_token() }}">
        @auth
            <meta name="user-id" content="{{ auth()->id() }}">
            <meta name="user-role" content="{{ auth()->user()->role }}">
        @endauth

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-light">

        <x-navbar :cart-count="$cartCount ?? 0" />

        <main class="py-4">
            <div class="container">
                @yield('content')
            </div>
        </main>

        @if(session('success') || session('error') || session('info'))

            <div class="toast-container position-fixed top-0 end-0 p-3">

                @if(session('success'))
                    <div class="toast align-items-center text-bg-success border-0 show">
                        <div class="d-flex">
                            <div class="toast-body">
                                {{ session('success') }}
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="toast align-items-center text-bg-danger border-0 show">
                        <div class="d-flex">
                            <div class="toast-body">
                                {{ session('error') }}
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('info'))
                    <div class="toast align-items-center text-bg-primary border-0 show">
                        <div class="d-flex">
                            <div class="toast-body">
                                {{ session('info') }}
                            </div>
                        </div>
                    </div>
                @endif

            </div>

        @endif

        @stack('scripts')

    </body>
    
</html>
