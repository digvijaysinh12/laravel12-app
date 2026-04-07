@extends('layouts.app')

@section('title', 'Permission')

@section('content')
<div class="mx-auto max-w-2xl rounded-[2rem] border border-dashed border-amber-300 bg-amber-50 px-6 py-16 text-center shadow-sm">
    <p class="text-xs uppercase tracking-[0.24em] text-amber-700">Warning</p>
    <h1 class="mt-3 text-3xl font-semibold text-slate-900">Permission required</h1>
    <p class="mt-3 text-sm text-slate-600">You do not have permission to access this area.</p>
    <div class="mt-6">
        <x-button href="{{ auth()->check() ? (auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard')) : route('login') }}">Go Back</x-button>
    </div>
</div>
@endsection
