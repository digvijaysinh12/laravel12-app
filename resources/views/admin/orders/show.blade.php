@extends('layouts.admin')

@section('page-title', 'Order Details')

@section('content')
<div class="space-y-6">
    <x-card title="Order summary">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-xs uppercase tracking-[0.24em] text-slate-500">Order No</div>
                <div class="mt-2 text-lg font-semibold text-slate-900">{{ $order->order_number }}</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-xs uppercase tracking-[0.24em] text-slate-500">Customer</div>
                <div class="mt-2 text-lg font-semibold text-slate-900">{{ $order->user->name ?? 'N/A' }}</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-xs uppercase tracking-[0.24em] text-slate-500">Total</div>
                <div class="mt-2 text-lg font-semibold text-slate-900">Rs. {{ number_format($order->total_amount, 2) }}</div>
            </div>
        </div>
    </x-card>

    <x-card title="Update status">
        <form method="POST" action="{{ route('admin.orders.status', $order->id) }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            @csrf
            @method('PUT')
            <select name="status" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-400 focus:bg-white sm:w-56">
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
                <tr class="hover:bg-slate-50">
                    <td class="px-3 py-4 text-slate-900">{{ $item->product->name ?? 'Deleted' }}</td>
                    <td class="px-3 py-4 text-slate-600">Rs. {{ number_format($item->price, 2) }}</td>
                    <td class="px-3 py-4 text-slate-600">{{ $item->quantity }}</td>
                    <td class="px-3 py-4 font-semibold text-slate-900">Rs. {{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-3 py-16 text-center text-sm text-slate-500">No items.</td>
                </tr>
            @endforelse
        </x-table>
    </x-card>
</div>
@endsection
