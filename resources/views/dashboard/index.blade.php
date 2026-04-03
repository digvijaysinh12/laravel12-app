@extends('layouts.app')

@section('title', 'Dashboard | ' . config('app.name'))

@section('content')
<div class="content-header">
    <div>
        <h2 class="no-margin">Dashboard</h2>
        <div class="content-header__meta">Welcome back, {{ auth()->user()->name }}</div>
    </div>
    <x-button href="{{ route('products.index') }}" class="btn-sm">View Products</x-button>
</div>

<div class="grid grid-3">
    <x-stat label="Total Products" :value="$totalProducts ?? 0" />
    <x-stat label="Total Users" :value="$totalUsers ?? 0" />
    <div class="stat">
        <div class="stat__label">App</div>
        <div class="stat__value">{{ config('app.name') }}</div>
    </div>
</div>

<x-card title="Quick Actions">
    <div class="d-flex flex-wrap gap-3">
        <x-button href="{{ route('admin.products.create') }}">Create Product</x-button>
        <x-button variant="secondary" href="{{ route('products.index') }}">Manage Products</x-button>
    </div>
</x-card>
@endsection
