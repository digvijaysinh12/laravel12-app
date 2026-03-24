@extends('layouts.app')

@section('content')

<div class="container mt-5">

    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-body">

            <h4 class="mb-4 text-center">Create Product</h4>

            {{-- 🔴 GLOBAL ERROR BOX --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- NAME --}}
                <div class="mb-3">
                    <label>Product Name</label>
                    <input type="text" name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}">

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- PRICE --}}
                <div class="mb-3">
                    <label>Price</label>
                    <input type="number" name="price"
                        class="form-control @error('price') is-invalid @enderror"
                        value="{{ old('price') }}">

                    @error('price')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- STOCK --}}
                <div class="mb-3">
                    <label>Stock</label>
                    <input type="number" name="stock"
                        class="form-control @error('stock') is-invalid @enderror"
                        value="{{ old('stock') }}">

                    @error('stock')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- DESCRIPTION --}}
                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- CATEGORY --}}
                <div class="mb-3">
                    <label>Category</label>

                    <select name="category_id"
                        class="form-control @error('category_id') is-invalid @enderror">

                        <option value="">-- Select Category --</option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('category_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- IMAGE --}}
                <div class="mb-3">
                    <label>Product Image</label>
                    <input type="file" name="image"
                        class="form-control @error('image') is-invalid @enderror">

                    @error('image')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- BUTTONS --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        Back
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Save Product
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

{{-- 🔥 AUTO FOCUS FIRST ERROR --}}
@if ($errors->any())
<script>
    document.querySelector('.is-invalid')?.focus();
</script>
@endif

@endsection