@extends('layouts.app')

@section('content')

    <div class="container mt-5">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm mx-auto" style="max-width: 500px;">
            <div class="card-body">

                <h4 class="mb-4 text-center fw-bold">✏ Edit Product</h4>

                <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}"
                            required>

                        @if ($errors->has('name'))
                            <span class="text-danger">
                                {{ $errors->first('name') }}
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}"
                            required>

                        @if ($errors->has('price'))
                            <span class="text-danger">
                                {{ $errors->first('price') }}
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control"
                            required>{{ old('description', $product->description) }}</textarea>

                        @if ($errors->has('description'))
                            <span class="text-danger">
                                {{ $errors->first('description') }}
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>

                        <select name="category_id" class="form-control">
                            <option value="">-- Select Category --</option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        @if ($errors->has('category_id'))
                            <span class="text-danger">
                                {{ $errors->first('category_id') }}
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Image</label><br>
                        <img src="{{ asset('storage/' . $product->image) }}" width="100" class="rounded shadow-sm mb-2">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Change Image</label>
                        <input type="file" name="image" class="form-control">

                        @if ($errors->has('image'))
                            <span class="text-danger">
                                {{ $errors->first('image') }}
                            </span>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            ⬅ Back
                        </a>

                        <button type="submit" class="btn btn-primary">
                            💾 Update
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

@endsection