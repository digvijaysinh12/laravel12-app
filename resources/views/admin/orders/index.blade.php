@extends('layouts.admin')

@section('page-title', 'Orders')

@section('content')
<x-card title="Orders">
    <x-table :headers="['#','Order No','User','Total','Status','Action']">
        @forelse($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->user->name ?? 'N/A' }}</td>
                <td>Rs. {{ number_format($order->total_amount, 2) }}</td>
                <td>
                    <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning text-dark' : 'info') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td>
                    <x-button href="{{ route('admin.orders.show', $order->id) }}" class="btn-sm">View</x-button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">No orders found</td>
            </tr>
        @endforelse
    </x-table>

    <div class="mt-3">
        {{ $orders->links() }}
    </div>
</x-card>
@endsection
