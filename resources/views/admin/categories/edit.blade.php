@extends('admin.layouts.app')

@section('page-title', 'Edit Category')

@section('content')
<x-admin.card title="Edit category" description="Update the category name.">
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-4 max-w-xl">
        @csrf
        @method('PUT')
        <x-admin.input name="name" label="Category name" :value="$category->name" required />
        <div class="flex justify-end gap-2">
            <x-admin.button href="{{ route('admin.categories.index') }}" variant="secondary">Cancel</x-admin.button>
            <x-admin.button type="submit">Update Category</x-admin.button>
        </div>
    </form>
</x-admin.card>
@endsection
