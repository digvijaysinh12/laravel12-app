@extends('user.layouts.app')

@section('title', '500')

@section('content')
@php($requestId = \Illuminate\Support\Facades\Context::get('request_id'))
<div class="mx-auto max-w-2xl rounded-[2rem] border border-dashed border-rose-300 bg-rose-50 px-6 py-16 text-center shadow-sm">
    <p class="text-xs uppercase tracking-[0.24em] text-rose-700">500</p>
    <h1 class="mt-3 text-3xl font-semibold text-slate-900">Server error</h1>
    <p class="mt-3 text-sm text-slate-600">Something went wrong while processing your request.</p>
    <div class="mt-6">
        <x-button href="{{ auth()->check() ? (auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard')) : route('login') }}">Go Back</x-button>
    </div>
    @if ($requestId)
        <p class="mt-6 text-xs text-rose-700/70">Request ID: {{ $requestId }}</p>
    @endif
</div>
@endsection
