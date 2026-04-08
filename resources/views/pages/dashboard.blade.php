@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $cartCount = count(session('cart', []));
@endphp

<div class="space-y-6">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr] lg:items-center">
            <div class="space-y-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">Customer dashboard</p>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-900">
                    Welcome back, {{ auth()->user()->name }}
                </h1>
                <p class="max-w-2xl text-sm leading-7 text-slate-600">
                    Continue browsing products, review your cart, and keep an eye on your order activity.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('user.products.index') }}" class="inline-flex items-center rounded-lg bg-sky-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700">
                        Browse Products
                    </a>
                    <a href="{{ route('user.cart.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Open Cart
                    </a>
                    <a href="{{ route('user.orders.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        View Orders
                    </a>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Email</p>
                    <p class="mt-2 text-sm font-medium text-slate-900">{{ auth()->user()->email }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Items in Cart</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $cartCount }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Quick Links</h2>
            <div class="mt-4 space-y-2 text-sm">
                <a href="{{ route('user.products.index') }}" class="block rounded-lg border border-slate-200 px-3 py-2 text-slate-700 transition hover:bg-slate-50">Shop Products</a>
                <a href="{{ route('user.profile.edit') }}" class="block rounded-lg border border-slate-200 px-3 py-2 text-slate-700 transition hover:bg-slate-50">Manage Profile</a>
                <a href="{{ route('user.orders.analytics') }}" class="block rounded-lg border border-slate-200 px-3 py-2 text-slate-700 transition hover:bg-slate-50">Order Analytics</a>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Cart Status</h2>
            <p class="mt-3 text-sm text-slate-600">Items currently in your cart</p>
            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $cartCount }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-slate-900">Shopping Tips</h2>
            <ul class="mt-3 space-y-2 text-sm text-slate-600">
                <li class="rounded-lg bg-slate-50 px-3 py-2">Use filters to narrow products quickly.</li>
                <li class="rounded-lg bg-slate-50 px-3 py-2">Check stock before checkout.</li>
                <li class="rounded-lg bg-slate-50 px-3 py-2">Download your invoice after ordering.</li>
            </ul>
        </div>
    </section>
</div>
@endsection
