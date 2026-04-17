@extends('admin.layouts.app')

@section('page-title', 'Orders')

@section('content')
<x-admin.card title="Orders" description="Track order status and fulfillment progress.">
    <div class="mb-4 flex justify-end">
        <x-admin.button href="{{ route('admin.orders.create') }}">Create Order</x-admin.button>
    </div>

    <x-admin.table :headers="['Order', 'Customer', 'Total', 'Status', 'Action']">
        @forelse ($orders as $order)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-900">{{ $order->order_number ?? '#'.$order->id }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $order->user->name ?? 'N/A' }}</td>
                <td class="px-4 py-3 text-slate-600">Rs. {{ number_format($order->total_amount, 2) }}</td>
                <td class="px-4 py-3">
                    <x-admin.badge :tone="$order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'neutral')">
                        {{ ucfirst($order->status) }}
                    </x-admin.badge>
                </td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-2">
                        <x-admin.button href="{{ route('admin.orders.show', $order) }}" variant="secondary" class="px-3 py-1.5 text-xs">View</x-admin.button>
                        <x-admin.button href="{{ route('admin.orders.edit', $order) }}" variant="secondary" class="px-3 py-1.5 text-xs">Edit</x-admin.button>
                        <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('Delete this order?');">
                            @csrf
                            @method('DELETE')
                            <x-admin.button type="submit" variant="danger" class="px-3 py-1.5 text-xs">Delete</x-admin.button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">No data found.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</x-admin.card>
@endsection
