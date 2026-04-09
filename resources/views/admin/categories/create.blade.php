@extends('admin.layouts.app')

@section('page-title', 'Create Category')

@section('content')
<x-admin.card title="Create category" description="Add a new category for the catalog.">
    <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4 max-w-xl">
        @csrf
        <x-admin.input name="name" label="Category name" :value="old('name')" required />
        <div class="flex justify-end gap-2">
            <x-admin.button href="{{ route('admin.categories.index') }}" variant="secondary">Cancel</x-admin.button>
            <x-admin.button type="submit">Save Category</x-admin.button>
        </div>
    </form>
</x-admin.card>
@endsection
