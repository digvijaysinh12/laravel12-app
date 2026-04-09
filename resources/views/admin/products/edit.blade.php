@extends('admin.layouts.app')

@section('page-title', 'Edit Product')

@section('content')
<x-admin.card title="Edit product" description="Update catalog details and inventory.">
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="grid gap-4 md:grid-cols-2">
        @csrf
        @method('PUT')

        <x-admin.input name="name" label="Product name" :value="$product->name" required />
        <x-admin.select name="category_id" label="Category" :options="$categories->pluck('name', 'id')->all()" :selected="$product->category_id" placeholder="Select category" required />
        <x-admin.input name="price" label="Price" type="number" :value="$product->price" step="0.01" required />
        <x-admin.input name="stock" label="Stock" type="number" :value="$product->stock" required />

        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
            <textarea name="description" rows="5" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200">{{ $product->description }}</textarea>
            @error('description')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @else
                <p class="text-xs text-transparent">.</p>
            @enderror
        </div>

        <div class="md:col-span-2 grid gap-4 md:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="mb-3 text-sm font-medium text-slate-700">Current image</p>
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-44 w-full rounded-lg object-cover">
                @else
                    <div class="flex h-44 items-center justify-center rounded-lg border border-dashed border-slate-300 text-sm text-slate-500">No image</div>
                @endif
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-slate-700">Replace image</label>
                <input type="file" name="image" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white">
                @error('image')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @else
                    <p class="text-xs text-transparent">.</p>
                @enderror
            </div>
        </div>

        <div class="md:col-span-2 flex items-center justify-end gap-2 pt-2">
            <x-admin.button href="{{ route('admin.products.index') }}" variant="secondary">Cancel</x-admin.button>
            <x-admin.button type="submit">Update Product</x-admin.button>
        </div>
    </form>
</x-admin.card>
@endsection
