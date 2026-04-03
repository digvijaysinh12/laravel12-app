@extends('layouts.admin')

@section('page-title', 'Order Details')

@section('content')
<div class="d-grid gap-3">
    <x-card title="Order Summary">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted text-uppercase small">Order No</div>
                <div class="fw-semibold">{{ $order->order_number }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted text-uppercase small">Customer</div>
                <div class="fw-semibold">{{ $order->user->name ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted text-uppercase small">Total</div>
                <div class="fw-semibold">Rs. {{ number_format($order->total_amount, 2) }}</div>
            </div>
        </div>
    </x-card>

    <x-card title="Update Status">
        <form method="POST" action="{{ route('admin.orders.status', $order->id) }}" class="d-flex align-items-center gap-3">
            @csrf
            @method('PUT')
            <select name="status" class="form-select w-auto">
                @foreach(['pending','confirmed','shipped','delivered','cancelled'] as $status)
                    <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
            <x-button type="submit">Update</x-button>
        </form>
    </x-card>

    <x-card title="Items">
        <x-table :headers="['Product','Price','Qty','Total']">
            @forelse($order->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Deleted' }}</td>
                    <td>Rs. {{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rs. {{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">No items</td>
                </tr>
            @endforelse
        </x-table>
    </x-card>
</div>
@endsection
