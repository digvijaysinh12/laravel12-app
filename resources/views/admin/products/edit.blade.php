@extends('layouts.admin')

@section('page-title', 'Edit Product')

@section('content')
<div class="mx-auto max-w-4xl">
    <x-card title="Edit product">
        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Product Name</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-400 focus:bg-white @error('name') border-rose-300 focus:border-rose-400 @enderror">
                    @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Category</label>
                    <select name="category_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-400 focus:bg-white @error('category_id') border-rose-300 focus:border-rose-400 @enderror">
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Price</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" required
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-400 focus:bg-white @error('price') border-rose-300 focus:border-rose-400 @enderror">
                    @error('price')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Stock</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-400 focus:bg-white @error('stock') border-rose-300 focus:border-rose-400 @enderror">
                    @error('stock')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                <textarea name="description" rows="5" required
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-400 focus:bg-white @error('description') border-rose-300 focus:border-rose-400 @enderror">{{ old('description', $product->description) }}</textarea>
                @error('description')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <label class="mb-3 block text-sm font-medium text-slate-700">Current Image</label>
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-40 w-full rounded-2xl object-cover">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Replace Image</label>
                    <input type="file" name="image"
                        class="w-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition file:mr-4 file:rounded-full file:border-0 file:bg-slate-950 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:border-slate-400">
                    @error('image')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center sm:justify-between">
                <x-button variant="secondary" href="{{ route('admin.products.index') }}">Back</x-button>
                <x-button type="submit">Update</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
