@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $cartCount = count(session('cart', []));
@endphp

<div class="space-y-8">
    <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
        <div class="grid gap-0 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-5 p-8 sm:p-10">
                <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Customer dashboard</p>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">
                    Welcome back, {{ auth()->user()->name }}
                </h1>
                <p class="max-w-2xl text-sm leading-7 text-slate-600">
                    Track your shopping activity, review your cart, and jump back into the product catalog whenever you need it.
                </p>

                <div class="flex flex-wrap gap-3">
                    <x-button href="{{ route('user.products.index') }}">Browse Products</x-button>
                    <x-button href="{{ route('user.cart.index') }}" variant="secondary">Open Cart</x-button>
                </div>
            </div>

            <div class="bg-gradient-to-br from-slate-950 to-slate-700 p-8 text-white sm:p-10">
                <div class="grid h-full gap-4">
                    <div class="rounded-[1.75rem] bg-white/10 p-5 backdrop-blur">
                        <div class="text-xs uppercase tracking-[0.24em] text-slate-300">Account</div>
                        <div class="mt-2 text-lg font-semibold">{{ auth()->user()->email }}</div>
                        <div class="mt-1 text-sm text-slate-300">Role: {{ auth()->user()->role }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-[1.75rem] bg-white/10 p-5 backdrop-blur">
                            <div class="text-xs uppercase tracking-[0.24em] text-slate-300">Cart items</div>
                            <div class="mt-2 text-3xl font-semibold">{{ $cartCount }}</div>
                        </div>
                        <div class="rounded-[1.75rem] bg-white/10 p-5 backdrop-blur">
                            <div class="text-xs uppercase tracking-[0.24em] text-slate-300">Profile</div>
                            <div class="mt-2 text-3xl font-semibold">1</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-3">
        <x-card title="Quick Links">
            <div class="space-y-3">
                <x-button href="{{ route('user.products.index') }}" class="w-full">Shop Products</x-button>
                <x-button href="{{ route('user.profile.edit') }}" variant="secondary" class="w-full">Edit Profile</x-button>
            </div>
        </x-card>

        <x-card title="Cart Status">
            <div class="rounded-2xl bg-slate-50 p-5">
                <div class="text-sm text-slate-500">Items currently in cart</div>
                <div class="mt-2 text-3xl font-semibold text-slate-900">{{ $cartCount }}</div>
                <p class="mt-2 text-sm text-slate-600">Open the cart to adjust quantities or checkout.</p>
            </div>
        </x-card>

        <x-card title="Shopping Tips">
            <div class="space-y-3 text-sm text-slate-600">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">Use the product catalog to discover new items.</div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">Keep an eye on stock badges before adding to cart.</div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">Invoices are available immediately after checkout.</div>
            </div>
        </x-card>
    </section>
</div>
@endsection
