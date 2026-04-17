@extends('admin.layouts.app')

@section('page-title', 'Order Details')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-3">
        <x-admin.card title="Order">
            <p class="text-lg font-semibold text-slate-900">{{ $order->order_number }}</p>
            <p class="mt-1 text-sm text-slate-500">Placed by {{ $order->user->name ?? 'N/A' }}</p>
        </x-admin.card>

        <x-admin.card title="Total">
            <p class="text-lg font-semibold text-slate-900">Rs. {{ number_format($order->total_amount, 2) }}</p>
            <p class="mt-1 text-sm text-slate-500">Order value</p>
        </x-admin.card>

        <x-admin.card title="Status">
            <x-admin.badge :tone="$order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'neutral')">
                {{ ucfirst($order->status) }}
            </x-admin.badge>
        </x-admin.card>
    </div>

    <div class="flex flex-wrap justify-end gap-3">
        <x-admin.button href="{{ route('admin.orders.edit', $order) }}" variant="secondary">Edit Order</x-admin.button>
        <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('Delete this order?');">
            @csrf
            @method('DELETE')
            <x-admin.button type="submit" variant="danger">Delete Order</x-admin.button>
        </form>
    </div>

    <x-admin.card title="Update status" description="Change fulfillment status for this order.">
        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            @csrf
            @method('PUT')
            <div class="w-full sm:max-w-xs">
                <label class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                <select name="status" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-200">
                    @foreach (['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <x-admin.button type="submit">Update</x-admin.button>
        </form>
    </x-admin.card>

    <x-admin.card title="Items" description="Products included in the order.">
        <x-admin.table :headers="['Product', 'Price', 'Qty', 'Total']">
            @forelse ($order->items as $item)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-900">{{ $item->product->name ?? 'Deleted' }}</td>
                    <td class="px-4 py-3 text-slate-600">Rs. {{ number_format($item->price, 2) }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $item->quantity }}</td>
                    <td class="px-4 py-3 font-medium text-slate-900">Rs. {{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">No data found.</td>
                </tr>
            @endforelse
        </x-admin.table>
    </x-admin.card>
</div>
@endsection
