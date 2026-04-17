@extends('user.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-8 rounded-2xl bg-gray-900 p-10 text-white">
        <h1 class="mb-2 text-4xl font-bold">Welcome to Our Store</h1>
        <p class="text-gray-300">Discover the latest products at the best price</p>
    </div>

    <section class="mb-10">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-2xl font-semibold">Featured Products</h2>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4">
            @foreach ($featured as $product)
                <div class="rounded-xl bg-white p-4 shadow transition hover:shadow-lg">
                    <img src="{{ asset('storage/' . $product->image) }}" class="mb-3 h-40 w-full rounded object-cover">

                    <h3 class="text-lg font-semibold">{{ $product->name }}</h3>

                    <p class="mb-2 text-sm text-gray-500">
                        {{ Str::limit($product->description, 50) }}
                    </p>

                    <div class="flex items-center justify-between">
                        <span class="font-bold text-blue-600">INR {{ number_format($product->price, 2) }}</span>
                        <a href="{{ route('user.products.show', $product) }}" class="rounded bg-blue-600 px-3 py-1 text-sm text-white">View</a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mb-10">
        <h2 class="mb-4 text-2xl font-semibold">New Arrivals</h2>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4">
            @foreach ($newArrivals as $product)
                <div class="rounded-xl bg-white p-4 shadow transition hover:shadow-lg">
                    <img src="{{ asset('storage/' . $product->image) }}" class="mb-3 h-40 w-full rounded object-cover">

                    <h3 class="text-lg font-semibold">{{ $product->name }}</h3>

                    <span class="font-bold text-green-600">INR {{ number_format($product->price, 2) }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mb-10">
        <h2 class="mb-4 text-2xl font-semibold">On Sale</h2>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4">
            @foreach ($onSale as $product)
                <div class="rounded-xl bg-white p-4 shadow transition hover:shadow-lg">
                    <img src="{{ asset('storage/' . $product->image) }}" class="mb-3 h-40 w-full rounded object-cover">

                    <h3 class="text-lg font-semibold">{{ $product->name }}</h3>

                    <span class="font-bold text-red-600">INR {{ number_format($product->price, 2) }}</span>
                </div>
            @endforeach
        </div>
    </section>

    @if (($recentlyViewedProducts ?? collect())->isNotEmpty())
        <section class="mb-10">
            <h2 class="mb-4 text-2xl font-semibold">Recently Viewed</h2>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4">
                @foreach ($recentlyViewedProducts as $product)
                    <div class="rounded-xl bg-white p-4 shadow transition hover:shadow-lg">
                        <img src="{{ asset('storage/' . $product->image) }}" class="mb-3 h-40 w-full rounded object-cover">

                        <h3 class="text-lg font-semibold">{{ $product->name }}</h3>

                        <div class="mt-3 flex items-center justify-between">
                            <span class="font-bold text-slate-900">INR {{ number_format($product->price, 2) }}</span>
                            <a href="{{ route('user.products.show', $product) }}" class="rounded bg-slate-900 px-3 py-1 text-sm text-white">View</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section>
        <h2 class="mb-4 text-2xl font-semibold">Categories</h2>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-6">
            @foreach ($categories as $category)
                <div class="rounded-lg bg-gray-100 p-4 text-center transition hover:bg-gray-200">
                    <h4 class="font-medium">{{ $category->name }}</h4>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
