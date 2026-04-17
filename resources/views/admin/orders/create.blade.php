@extends('admin.layouts.app')

@section('page-title', 'Create Order')

@section('content')
<div class="space-y-6">
    <section class="rounded-xl border bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-semibold text-slate-900">Create Order</h1>
        <p class="mt-2 text-sm text-slate-500">Create a manual order for a customer from the admin panel.</p>
    </section>

    @include('admin.orders.partials.form', [
        'action' => route('admin.orders.store'),
        'submitLabel' => 'Create Order',
    ])
</div>
@endsection
