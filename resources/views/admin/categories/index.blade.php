@extends('admin.layouts.app')

@section('page-title', 'Categories')

@section('content')
<x-admin.card title="Categories" description="Organize products into simple collections.">
    <div class="mb-4 flex items-center justify-end">
        <x-admin.button href="{{ route('admin.categories.create') }}">Add Category</x-admin.button>
    </div>

    <x-admin.table :headers="['Category', 'Products', 'Actions']">
        @forelse ($categories as $category)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 font-medium text-slate-900">{{ $category->name }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $category->products_count }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <x-admin.button href="{{ route('admin.categories.edit', $category) }}" variant="secondary" class="px-3 py-1.5 text-xs">Edit</x-admin.button>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                            @csrf
                            @method('DELETE')
                            <x-admin.button type="submit" variant="danger" class="px-3 py-1.5 text-xs">Delete</x-admin.button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="px-4 py-10 text-center text-sm text-slate-500">No data found.</td>
            </tr>
        @endforelse
    </x-admin.table>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</x-admin.card>
@endsection
