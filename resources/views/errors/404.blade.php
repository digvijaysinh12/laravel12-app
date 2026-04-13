@extends('user.layouts.app')

@section('title', '404')

@section('content')
@php($requestId = \Illuminate\Support\Facades\Context::get('request_id'))
<div class="mx-auto max-w-2xl rounded-[2rem] border border-dashed border-slate-300 bg-white px-6 py-16 text-center shadow-sm">
    <p class="text-xs uppercase tracking-[0.24em] text-slate-500">404</p>
    <h1 class="mt-3 text-3xl font-semibold text-slate-900">Page not found</h1>
    <p class="mt-3 text-sm text-slate-500">The page you are looking for does not exist or has moved.</p>
    <div class="mt-6">
        <x-button href="{{ auth()->check() ? (auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard')) : route('login') }}">Go Back</x-button>
    </div>
    @if ($requestId)
        <p class="mt-6 text-xs text-slate-400">Request ID: {{ $requestId }}</p>
    @endif
</div>
@endsection
