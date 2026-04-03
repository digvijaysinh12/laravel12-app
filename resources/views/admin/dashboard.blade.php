@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="d-grid gap-3">
    <div class="grid grid-3">
        <x-stat label="Total Products" :value="$totalProducts ?? '—'" />
        <x-stat label="Total Users" :value="$totalUsers ?? '—'" />
        <x-stat label="Pending Orders" :value="$pendingOrders ?? '—'" />
    </div>

    <x-card title="Quick Actions" :actions="null">
        <div class="d-flex gap-2 flex-wrap">
            <x-button href="{{ route('admin.products.create') }}">Create Product</x-button>
            <x-button variant="secondary" href="{{ route('admin.orders.index') }}">Manage Orders</x-button>
            @if(Route::has('admin.reports.index'))
                <x-button variant="ghost" href="{{ route('admin.reports.index') }}">View Reports</x-button>
            @endif
        </div>
    </x-card>
</div>
@endsection
