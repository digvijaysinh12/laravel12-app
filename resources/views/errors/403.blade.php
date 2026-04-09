@extends('user.layouts.app')

@section('title', '403')

@section('content')
<div class="mx-auto max-w-2xl rounded-[2rem] border border-dashed border-slate-300 bg-white px-6 py-16 text-center shadow-sm">
    <p class="text-xs uppercase tracking-[0.24em] text-slate-500">403</p>
    <h1 class="mt-3 text-3xl font-semibold text-slate-900">Forbidden</h1>
    <p class="mt-3 text-sm text-slate-500">You do not have permission to access this page.</p>
    <div class="mt-6">
        <x-button href="{{ auth()->check() ? (auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard')) : route('login') }}">Go Back</x-button>
    </div>
</div>
@endsection
