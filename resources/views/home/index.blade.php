@extends('user.layouts.app')

@section('content')

@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700 shadow-sm">
        {{ $errors->first() }}
    </div>
@endif

<div class="mx-auto max-w-7xl px-4 py-8">

    {{-- Hero Section --}}
    <div class="relative overflow-hidden rounded-3xl bg-zinc-950 px-10 py-20 text-white shadow-2xl">

        <div class="max-w-2xl">

            <p class="mb-3 text-sm font-medium uppercase tracking-[0.25em] text-zinc-400">
                Premium Collection
            </p>

            <h1 class="text-5xl font-bold leading-tight tracking-tight">
                Welcome to Our Store
            </h1>

            <p class="mt-5 text-lg leading-relaxed text-zinc-300">
                Discover the latest products at the best price with modern quality and trusted experience.
            </p>

            <div class="mt-8 flex gap-4">

                <a href="{{ route('user.products.index') }}"
                   class="rounded-2xl bg-white px-6 py-3 text-sm font-semibold text-zinc-900 transition hover:bg-zinc-200">

                    Shop Now

                </a>

                <a href="#featured"
                   class="rounded-2xl border border-zinc-700 px-6 py-3 text-sm font-semibold text-white transition hover:border-zinc-500 hover:bg-zinc-900">

                    Explore

                </a>

            </div>

        </div>

        {{-- Background Accent --}}
        <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-white/5 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 h-48 w-48 rounded-full bg-white/5 blur-2xl"></div>

    </div>

    {{-- Featured --}}
    <section id="featured" class="mt-16">

        <div class="mb-8 flex items-center justify-between">

            <div>
                <h2 class="text-3xl font-bold tracking-tight text-zinc-900">
                    Featured Products
                </h2>

                <p class="mt-1 text-sm text-zinc-500">
                    Handpicked premium products for you
                </p>
            </div>

        </div>

        <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 xl:grid-cols-4">

            @foreach ($featured as $product)

                <div class="group overflow-hidden rounded-3xl border border-zinc-200 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">

                    {{-- Image --}}
                    <div class="relative overflow-hidden bg-zinc-100">

                        <img src="{{ asset('storage/' . $product->image) }}"
                             class="h-64 w-full object-cover transition duration-500 group-hover:scale-105">

                    </div>

                    {{-- Content --}}
                    <div class="p-5">

                        <h3 class="text-lg font-semibold text-zinc-900">
                            {{ $product->name }}
                        </h3>

                        <p class="mt-2 text-sm leading-relaxed text-zinc-500">
                            {{ Str::limit($product->description, 60) }}
                        </p>

                        <div class="mt-5 flex items-center justify-between">

                            <span class="text-lg font-bold text-zinc-900">
                                INR {{ number_format($product->price, 2) }}
                            </span>

                            <a href="{{ route('user.products.show', $product) }}"
                               class="rounded-xl bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-black">

                                View

                            </a>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </section>

    {{-- New Arrivals --}}
    <section class="mt-20">

        <div class="mb-8">
            <h2 class="text-3xl font-bold tracking-tight text-zinc-900">
                New Arrivals
            </h2>

            <p class="mt-1 text-sm text-zinc-500">
                Recently added products
            </p>
        </div>

        <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 xl:grid-cols-4">

            @foreach ($newArrivals as $product)

                <div class="group rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm transition hover:shadow-xl">

                    <div class="overflow-hidden rounded-2xl bg-zinc-100">

                        <img src="{{ asset('storage/' . $product->image) }}"
                             class="h-56 w-full object-cover transition duration-500 group-hover:scale-105">

                    </div>

                    <div class="mt-4">

                        <h3 class="text-lg font-semibold text-zinc-900">
                            {{ $product->name }}
                        </h3>

                        <p class="mt-3 text-lg font-bold text-zinc-900">
                            INR {{ number_format($product->price, 2) }}
                        </p>

                    </div>

                </div>

            @endforeach

        </div>

    </section>

    {{-- On Sale --}}
    <section class="mt-20">

        <div class="mb-8">
            <h2 class="text-3xl font-bold tracking-tight text-zinc-900">
                On Sale
            </h2>

            <p class="mt-1 text-sm text-zinc-500">
                Best deals available right now
            </p>
        </div>

        <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 xl:grid-cols-4">

            @foreach ($onSale as $product)

                <div class="group rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm transition hover:shadow-xl">

                    <div class="overflow-hidden rounded-2xl bg-zinc-100">

                        <img src="{{ asset('storage/' . $product->image) }}"
                             class="h-56 w-full object-cover transition duration-500 group-hover:scale-105">

                    </div>

                    <div class="mt-4">

                        <h3 class="text-lg font-semibold text-zinc-900">
                            {{ $product->name }}
                        </h3>

                        <p class="mt-3 text-lg font-bold text-red-600">
                            INR {{ number_format($product->price, 2) }}
                        </p>

                    </div>

                </div>

            @endforeach

        </div>

    </section>

    {{-- Recently Viewed --}}
    @if (($recentlyViewedProducts ?? collect())->isNotEmpty())

        <section class="mt-20">

            <div class="mb-8">
                <h2 class="text-3xl font-bold tracking-tight text-zinc-900">
                    Recently Viewed
                </h2>

                <p class="mt-1 text-sm text-zinc-500">
                    Continue exploring products
                </p>
            </div>

            <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 xl:grid-cols-4">

                @foreach ($recentlyViewedProducts as $product)

                    <div class="group rounded-3xl border border-zinc-200 bg-white p-4 shadow-sm transition hover:shadow-xl">

                        <div class="overflow-hidden rounded-2xl bg-zinc-100">

                            <img src="{{ asset('storage/' . $product->image) }}"
                                 class="h-56 w-full object-cover transition duration-500 group-hover:scale-105">

                        </div>

                        <div class="mt-4">

                            <h3 class="text-lg font-semibold text-zinc-900">
                                {{ $product->name }}
                            </h3>

                            <div class="mt-4 flex items-center justify-between">

                                <span class="text-lg font-bold text-zinc-900">
                                    INR {{ number_format($product->price, 2) }}
                                </span>

                                <a href="{{ route('user.products.show', $product) }}"
                                   class="rounded-xl bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-black">

                                    View

                                </a>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </section>

    @endif

    {{-- Categories --}}
    <section class="mt-20">

        <div class="mb-8">
            <h2 class="text-3xl font-bold tracking-tight text-zinc-900">
                Categories
            </h2>

            <p class="mt-1 text-sm text-zinc-500">
                Browse by product category
            </p>
        </div>

        <div class="grid grid-cols-2 gap-5 md:grid-cols-3 xl:grid-cols-6">

            @foreach ($categories as $category)

                <div class="rounded-2xl border border-zinc-200 bg-white px-5 py-8 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-lg">

                    <h4 class="font-semibold text-zinc-800">
                        {{ $category->name }}
                    </h4>

                </div>

            @endforeach

        </div>

    </section>

</div>

@endsection

