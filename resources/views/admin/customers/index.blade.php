@extends('admin.layouts.app')

@section('page-title', 'Customers')

@section('content')
<x-admin.card title="Customers" description="View customer accounts and order activity.">
    <div class="mb-4 flex justify-end">
        <x-admin.button href="{{ route('admin.customers.create') }}">Create Customer</x-admin.button>
    </div>

    <x-admin.table :headers="['ID', 'Name', 'Email', 'Orders', 'Action']">
        @forelse ($customers as $customer)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-500">{{ $customer->id }}</td>
                <td class="px-4 py-3 font-medium text-slate-900">{{ $customer->name }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $customer->email }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $customer->orders_count }}</td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-2">
                        <x-admin.button href="{{ route('admin.customers.show', $customer) }}" variant="secondary" class="px-3 py-1.5 text-xs">View</x-admin.button>
                        <x-admin.button href="{{ route('admin.customers.edit', $customer) }}" variant="secondary" class="px-3 py-1.5 text-xs">Edit</x-admin.button>
                        <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" onsubmit="return confirm('Delete this customer account?');">
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
        {{ $customers->links() }}
    </div>
</x-admin.card>
@endsection
