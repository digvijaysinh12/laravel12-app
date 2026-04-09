@extends('user.layouts.app')

@section('content')

<div class="container mx-auto px-4 py-6">

    {{--  Hero Section --}}
    <div class="bg-gray-900 text-white rounded-2xl p-10 mb-8">
        <h1 class="text-4xl font-bold mb-2">Welcome to Our Store</h1>
        <p class="text-gray-300">Discover the latest products at the best price</p>
    </div>

    {{--  Featured Products --}}
    <section class="mb-10">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold">Featured Products</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($featured as $product)
                <div class="bg-white shadow rounded-xl p-4 hover:shadow-lg transition">
                    <img src="{{ asset('images/' . $product->image) }}" class="w-full h-40 object-cover rounded mb-3">
                    
                    <h3 class="font-semibold text-lg">{{ $product->name }}</h3>
                    
                    <p class="text-gray-500 text-sm mb-2">
                        {{ Str::limit($product->description, 50) }}
                    </p>

                    <div class="flex justify-between items-center">
                        <span class="text-blue-600 font-bold">₹{{ $product->price }}</span>
                        <a href="#" class="text-sm bg-blue-600 text-white px-3 py-1 rounded">View</a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- New Arrivals --}}
    <section class="mb-10">
        <h2 class="text-2xl font-semibold mb-4">New Arrivals</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($newArrivals as $product)
                <div class="bg-white shadow rounded-xl p-4 hover:shadow-lg transition">
                    <img src="{{ asset('images/' . $product->image) }}" class="w-full h-40 object-cover rounded mb-3">

                    <h3 class="font-semibold text-lg">{{ $product->name }}</h3>

                    <span class="text-green-600 font-bold">₹{{ $product->price }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 💸 On Sale (Random Products) --}}
    <section class="mb-10">
        <h2 class="text-2xl font-semibold mb-4">On Sale</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($onSale as $product)
                <div class="bg-white shadow rounded-xl p-4 hover:shadow-lg transition">
                    <img src="{{ asset('images/' . $product->image) }}" class="w-full h-40 object-cover rounded mb-3">

                    <h3 class="font-semibold text-lg">{{ $product->name }}</h3>

                    <span class="text-red-600 font-bold">₹{{ $product->price }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 🗂 Categories --}}
    <section>
        <h2 class="text-2xl font-semibold mb-4">Categories</h2>

        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            @foreach($categories as $category)
                <div class="bg-gray-100 p-4 text-center rounded-lg hover:bg-gray-200 transition">
                    <h4 class="font-medium">{{ $category->name }}</h4>
                </div>
            @endforeach
        </div>
    </section>

</div>

@endsection