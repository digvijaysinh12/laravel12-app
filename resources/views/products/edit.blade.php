@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        @if($errors->any())
            <x-alert type="danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <x-card title="Edit Product">
            <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="d-grid gap-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="form-label">Price</label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product->price) }}" required>
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description', $product->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">-- Select Category --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-grid gap-2">
                    <div>
                        <label class="form-label">Current Image</label><br>
                        <img src="{{ asset('storage/' . $product->image) }}" width="120" class="rounded shadow-sm">
                    </div>
                    <div>
                        <label class="form-label">Change Image</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <x-button variant="secondary" href="{{ route('products.index') }}">Back</x-button>
                    <x-button type="submit">Update</x-button>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection
