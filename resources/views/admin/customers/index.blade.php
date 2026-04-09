@extends('admin.layouts.app')

@section('page-title', 'Customers')

@section('content')
<x-admin.card title="Customers" description="View customer accounts and order activity.">
    <x-admin.table :headers="['ID', 'Name', 'Email', 'Orders', 'Action']">
        @forelse ($customers as $customer)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-500">{{ $customer->id }}</td>
                <td class="px-4 py-3 font-medium text-slate-900">{{ $customer->name }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $customer->email }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $customer->orders_count }}</td>
                <td class="px-4 py-3">
                    <x-admin.button href="{{ route('admin.customers.show', $customer) }}" variant="secondary" class="px-3 py-1.5 text-xs">View</x-admin.button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">No data found.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</x-admin.card>
@endsection
