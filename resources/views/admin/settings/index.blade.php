@extends('admin.layouts.app')

@section('page-title', 'Settings')

@section('content')
<x-admin.card title="Settings" description="Basic store settings layout.">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="grid gap-4 md:grid-cols-2">
        @csrf
        <x-admin.input name="store_name" label="Store name" value="{{ config('app.name') }}" />
        <x-admin.input name="support_email" label="Support email" type="email" value="support@example.com" />
        <x-admin.input name="currency" label="Currency" value="INR" />
        <x-admin.input name="timezone" label="Timezone" value="Asia/Calcutta" />
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Store note</label>
            <textarea rows="5" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-200">Minimal admin settings layout.</textarea>
            <p class="text-xs text-transparent">.</p>
        </div>
        <div class="md:col-span-2 flex justify-end">
            <x-admin.button type="submit">Save Settings</x-admin.button>
        </div>
    </form>
</x-admin.card>
@endsection
