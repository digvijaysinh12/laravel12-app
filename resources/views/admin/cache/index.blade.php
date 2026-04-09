@extends('admin.layouts.app')

@section('page-title', 'Cache Monitor')

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-2">
        <x-admin.card title="Cache stats" description="Monitor tags and cache usage.">
            <pre class="overflow-auto rounded-lg bg-slate-50 p-4 text-xs text-slate-700">{{ json_encode($stats, JSON_PRETTY_PRINT) }}</pre>
        </x-admin.card>

        <x-admin.card title="Actions" description="Clear cache safely when needed.">
            <form method="POST" action="{{ route('admin.cache.clear') }}" class="space-y-3">
                @csrf
                <x-admin.button type="submit" variant="danger">Clear All Cache</x-admin.button>
            </form>
        </x-admin.card>
    </div>
</div>
@endsection
