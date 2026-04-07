@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
    <div class="grid gap-0 lg:grid-cols-2">
        <div class="space-y-6 p-8 sm:p-12">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Product management</p>
            <h1 class="text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">
                Welcome to {{ config('app.name') }}
            </h1>
            <p class="max-w-xl text-base leading-7 text-slate-600">
                A clean storefront and admin workspace for managing products, carts, invoices, and orders without the clutter.
            </p>

            <div class="flex flex-wrap gap-3">
                @guest
                    <x-button href="{{ route('login') }}">Login</x-button>
                    <x-button href="{{ route('register') }}" variant="secondary">Register</x-button>
                @endguest

                @auth
                    <x-button href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}">Go to Dashboard</x-button>
                @endauth
            </div>
        </div>

        <div class="bg-gradient-to-br from-slate-950 to-slate-700 p-8 text-white sm:p-12">
            <div class="grid h-full gap-4">
                <div class="rounded-[2rem] bg-white/10 p-6 backdrop-blur">
                    <div class="text-sm uppercase tracking-[0.24em] text-slate-300">Built for scale</div>
                    <p class="mt-3 text-lg leading-7 text-slate-100">Separate admin and user layouts keep the project maintainable as features grow.</p>
                </div>
                <div class="rounded-[2rem] bg-white/10 p-6 backdrop-blur">
                    <div class="text-sm uppercase tracking-[0.24em] text-slate-300">Responsive by default</div>
                    <p class="mt-3 text-lg leading-7 text-slate-100">Sidebar, topbar, dashboards, and forms adapt smoothly across desktop and mobile screens.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
