<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}@hasSection('title') - @yield('title')@endif</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="auth-user-id" content="{{ auth()->id() }}">
        <meta name="auth-user-role" content="{{ auth()->user()->role }}">
    @endauth

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <x-navbar />

    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        @if (session('success') || session('error') || session('status'))
            <div class="pt-5 sm:pt-6">
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
            </div>
        @endif

        <main class="py-6 sm:py-8">
            @yield('content')
        </main>
    </div>

    <footer class="mt-12 border-t border-slate-200 bg-white">
        <div class="mx-auto grid w-full max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8">
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-[0.12em] text-slate-500">{{ config('app.name') }}</h3>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    A reliable storefront experience with secure checkout, fast browsing, and clean product discovery.
                </p>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-900">Company</h3>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li><a href="#" class="hover:text-slate-900">About</a></li>
                    <li><a href="#" class="hover:text-slate-900">Contact</a></li>
                    <li><a href="#" class="hover:text-slate-900">Careers</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-900">Policies</h3>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li><a href="#" class="hover:text-slate-900">Shipping Policy</a></li>
                    <li><a href="#" class="hover:text-slate-900">Return Policy</a></li>
                    <li><a href="#" class="hover:text-slate-900">Privacy Policy</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-slate-900">Follow Us</h3>
                <div class="mt-3 flex items-center gap-3 text-slate-500">
                    <a href="#" aria-label="Facebook" class="rounded-lg border border-slate-200 p-2 hover:border-slate-300 hover:text-slate-800">
                        <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current"><path d="M13.5 21v-7h2.3l.4-3h-2.7V9.2c0-.9.3-1.5 1.6-1.5H16V5.1c-.3 0-1.1-.1-2.1-.1-2 0-3.4 1.2-3.4 3.6V11H8v3h2.4v7h3.1Z"/></svg>
                    </a>
                    <a href="#" aria-label="Instagram" class="rounded-lg border border-slate-200 p-2 hover:border-slate-300 hover:text-slate-800">
                        <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current"><path d="M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4Zm0 2a2 2 0 0 0-2 2v10c0 1.1.9 2 2 2h10a2 2 0 0 0 2-2V7c0-1.1-.9-2-2-2H7Zm10.5 1.5a1 1 0 1 1 0 2 1 1 0 0 1 0-2ZM12 8a4 4 0 1 1 0 8 4 4 0 0 1 0-8Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/></svg>
                    </a>
                    <a href="#" aria-label="Twitter" class="rounded-lg border border-slate-200 p-2 hover:border-slate-300 hover:text-slate-800">
                        <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current"><path d="M18.9 7.2c.8-.1 1.5-.5 2.1-1-.3.9-.9 1.6-1.7 2.1v.5c0 5.2-4 11.2-11.2 11.2-2.2 0-4.2-.6-5.9-1.7h.8c1.8 0 3.4-.6 4.7-1.7a4 4 0 0 1-3.7-2.8h.7c.3 0 .6 0 .9-.1a4 4 0 0 1-3.2-3.9v-.1c.5.3 1 .4 1.6.4a4 4 0 0 1-1.2-5.3 11.3 11.3 0 0 0 8.2 4.2 4 4 0 0 1 6.8-3.6c.9-.2 1.7-.5 2.4-.9-.3.9-.9 1.6-1.7 2.1Z"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t border-slate-200">
            <div class="mx-auto w-full max-w-7xl px-4 py-4 text-sm text-slate-500 sm:px-6 lg:px-8">
                &copy; {{ now()->year }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
