@extends('admin.layouts.app')

@section('page-title', 'Create Product')

@section('content')
<x-admin.card title="Create product" description="Add a new item to the catalog.">
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
        @csrf

        <x-admin.input name="name" label="Product name" :value="old('name')" required />
        <x-admin.select name="category_id" label="Category" :options="$categories->pluck('name', 'id')->all()" :selected="old('category_id')" placeholder="Select category" required />
        <x-admin.input name="price" label="Price" type="number" :value="old('price')" step="0.01" required />
        <x-admin.input name="stock" label="Stock" type="number" :value="old('stock')" required />

        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
            <textarea name="description" rows="5" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200">{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @else
                <p class="text-xs text-transparent">.</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Image</label>
            <input type="file" name="image" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white">
            @error('image')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @else
                <p class="text-xs text-transparent">.</p>
            @enderror
        </div>

        <div class="md:col-span-2 flex items-center justify-end gap-2 pt-2">
            <x-admin.button href="{{ route('admin.products.index') }}" variant="secondary">Cancel</x-admin.button>
            <x-admin.button type="submit">Save Product</x-admin.button>
        </div>
    </form>
</x-admin.card>
@endsection
