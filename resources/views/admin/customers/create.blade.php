@extends('admin.layouts.app')

@section('page-title', 'Create Customer')

@section('content')
<section class="rounded-xl border bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Create Customer</h1>
        <p class="mt-2 text-sm text-slate-500">Add a new customer account that can sign in to the storefront.</p>
    </div>

    <form method="POST" action="{{ route('admin.customers.store') }}" class="grid gap-4 md:grid-cols-2">
        @csrf

        <x-admin.input name="name" label="Name" :value="old('name')" placeholder="Enter customer name" required />
        <x-admin.input name="email" label="Email" type="email" :value="old('email')" placeholder="Enter customer email" required />
        <x-admin.input name="password" label="Password" type="password" placeholder="Enter password" required />
        <x-admin.input name="password_confirmation" label="Confirm Password" type="password" placeholder="Re-enter password" required />

        <div class="md:col-span-2 flex justify-end gap-3">
            <x-admin.button href="{{ route('admin.customers.index') }}" variant="secondary">Cancel</x-admin.button>
            <x-admin.button type="submit">Create Customer</x-admin.button>
        </div>
    </form>
</section>
@endsection
