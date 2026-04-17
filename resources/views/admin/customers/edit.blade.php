@extends('admin.layouts.app')

@section('page-title', 'Edit Customer')

@section('content')
<section class="rounded-xl border bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Edit {{ $customer->name }}</h1>
        <p class="mt-2 text-sm text-slate-500">Update customer account details. Leave password blank to keep the current one.</p>
    </div>

    <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="grid gap-4 md:grid-cols-2">
        @csrf
        @method('PUT')

        <x-admin.input name="name" label="Name" :value="$customer->name" placeholder="Enter customer name" required />
        <x-admin.input name="email" label="Email" type="email" :value="$customer->email" placeholder="Enter customer email" required />
        <x-admin.input name="password" label="New Password" type="password" placeholder="Leave blank to keep current password" />
        <x-admin.input name="password_confirmation" label="Confirm New Password" type="password" placeholder="Re-enter new password" />

        <div class="md:col-span-2 flex justify-end gap-3">
            <x-admin.button href="{{ route('admin.customers.show', $customer) }}" variant="secondary">Cancel</x-admin.button>
            <x-admin.button type="submit">Save Changes</x-admin.button>
        </div>
    </form>
</section>
@endsection
