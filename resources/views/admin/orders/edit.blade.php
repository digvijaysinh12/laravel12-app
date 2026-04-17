@extends('admin.layouts.app')

@section('page-title', 'Edit Order')

@section('content')
<div class="space-y-6">
    <section class="rounded-xl border bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-semibold text-slate-900">Edit {{ $order->order_number }}</h1>
        <p class="mt-2 text-sm text-slate-500">Update customer details, order items, and fulfillment state.</p>
    </section>

    @include('admin.orders.partials.form', [
        'action' => route('admin.orders.update', $order),
        'method' => 'PUT',
        'submitLabel' => 'Save Changes',
    ])
</div>
@endsection
