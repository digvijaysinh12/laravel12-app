@extends('admin.layouts.app')

@section('page-title', 'Customer Details')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-3">
        <x-admin.card title="Customer">
            <p class="text-lg font-semibold text-slate-900">{{ $customer->name }}</p>
            <p class="mt-1 text-sm text-slate-500">{{ $customer->email }}</p>
        </x-admin.card>

        <x-admin.card title="Orders">
            <p class="text-lg font-semibold text-slate-900">{{ $customer->orders_count }}</p>
            <p class="mt-1 text-sm text-slate-500">Total placed orders</p>
        </x-admin.card>

        <x-admin.card title="Account">
            <p class="text-lg font-semibold text-slate-900">{{ $customer->role ?? 'customer' }}</p>
            <p class="mt-1 text-sm text-slate-500">Current role</p>
        </x-admin.card>
    </div>

    <div class="flex flex-wrap justify-end gap-3">
        <x-admin.button href="{{ route('admin.customers.edit', $customer) }}" variant="secondary">Edit Customer</x-admin.button>
        <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" onsubmit="return confirm('Delete this customer account?');">
            @csrf
            @method('DELETE')
            <x-admin.button type="submit" variant="danger">Delete Customer</x-admin.button>
        </form>
    </div>

    <x-admin.card title="Order history" description="Most recent orders for this customer.">
        <x-admin.table :headers="['Order', 'Date', 'Total', 'Status']">
            @forelse ($orders as $order)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $order->order_number }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ optional($order->created_at)->format('d M, Y') }}</td>
                    <td class="px-4 py-3 text-slate-600">Rs. {{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-4 py-3">
                        <x-admin.badge :tone="$order->status === 'delivered' ? 'success' : ($order->status === 'pending' ? 'warning' : 'neutral')">
                            {{ ucfirst($order->status) }}
                        </x-admin.badge>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">No data found.</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </x-admin.card>
</div>
@endsection
