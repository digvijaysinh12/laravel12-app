@extends('user.layouts.app')

@section('title', $product->name)

@section('content')
@php
    $stock = (int) ($product->stock ?? 0);
@endphp

<div class="space-y-6" data-product-detail data-product-id="{{ $product->id }}">
    <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="aspect-square bg-slate-50">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full items-center justify-center text-sm text-slate-500">No image available</div>
                @endif
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $product->category->name ?? 'Uncategorized' }}</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">{{ $product->name }}</h1>

            <div class="mt-4 flex flex-wrap items-center gap-3">
                <span class="text-2xl font-semibold text-slate-900">INR {{ number_format($product->price, 2) }}</span>
                <span class="product-stock-badge inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $stock > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}" data-product-id="{{ $product->id }}">
                    {{ $stock > 0 ? "Stock: $stock" : 'Out of Stock' }}
                </span>
                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                    Rating: {{ number_format((float) $product->rating, 1) }}/5
                </span>
            </div>

            <p class="mt-4 text-sm leading-7 text-slate-600">
                {{ $product->description ?: 'No description available for this product yet.' }}
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('user.products.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    Back to Products
                </a>

                @if (auth()->check() && auth()->user()->role === 'user')
                    <form action="{{ route('user.cart.add', $product->id) }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="add-to-cart-btn inline-flex items-center rounded-lg bg-sky-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                            data-product-id="{{ $product->id }}"
                            @disabled($stock <= 0)
                        >
                            {{ $stock <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                        </button>
                    </form>
                @endif
            </div>
        </section>
    </div>

    <section class="grid gap-6 lg:grid-cols-[1fr_1fr]">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Customer Reviews</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $product->reviews->count() }} approved reviews</p>
                </div>
                <div class="rounded-xl bg-slate-100 px-4 py-3 text-center">
                    <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Average</p>
                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ number_format((float) $product->rating, 1) }}</p>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($product->reviews as $review)
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="font-medium text-slate-900">{{ $review->user->name ?? 'Customer' }}</p>
                                <p class="text-xs text-slate-500">{{ $review->created_at?->format('d M Y') }}</p>
                            </div>
                            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                {{ $review->rating }}/5
                            </span>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $review->comment ?: 'No comment provided.' }}</p>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center text-sm text-slate-500">
                        No approved reviews yet.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-xl font-semibold text-slate-900">Write a Review</h2>
            <p class="mt-1 text-sm text-slate-500">Only customers who purchased this product can submit a review.</p>

            @if (auth()->check() && auth()->user()->role === 'user')
                @if ($userReview)
                    <div class="mt-4 rounded-xl border {{ $userReview->is_approved ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-amber-200 bg-amber-50 text-amber-800' }} px-4 py-3 text-sm">
                        {{ $userReview->is_approved ? 'Your review is live on the product page.' : 'Your latest review is waiting for admin approval.' }}
                    </div>
                @endif

                <form method="POST" action="{{ route('user.products.reviews.store', $product) }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="rating" class="mb-1 block text-sm font-medium text-slate-700">Rating</label>
                        <select id="rating" name="rating" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900">
                            @foreach ([5, 4, 3, 2, 1] as $rating)
                                <option value="{{ $rating }}" @selected((int) old('rating', $userReview?->rating) === $rating)>{{ $rating }} / 5</option>
                            @endforeach
                        </select>
                        @error('rating')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="comment" class="mb-1 block text-sm font-medium text-slate-700">Comment</label>
                        <textarea id="comment" name="comment" rows="5" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm text-slate-700">{{ old('comment', $userReview?->comment) }}</textarea>
                        @error('comment')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="inline-flex items-center rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700">
                            {{ $userReview ? 'Update Review' : 'Submit Review' }}
                        </button>
                    </div>
                </form>

                @if ($userReview)
                    <form method="POST" action="{{ route('user.products.reviews.destroy', $product) }}" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center rounded-lg border border-rose-300 px-4 py-2.5 text-sm font-medium text-rose-700 transition hover:bg-rose-50">
                            Remove Review
                        </button>
                    </form>
                @endif
            @else
                <div class="mt-6 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-5 py-10 text-center text-sm text-slate-500">
                    Sign in with a customer account to review this product.
                </div>
            @endif
        </article>
    </section>

    @if (($recentlyViewedProducts ?? collect())->isNotEmpty())
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-600">Recently Viewed</p>
                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">Continue Browsing</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($recentlyViewedProducts as $recentProduct)
                    <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="h-40 overflow-hidden rounded-xl bg-slate-100">
                            @if ($recentProduct->image)
                                <img src="{{ asset('storage/' . $recentProduct->image) }}" alt="{{ $recentProduct->name }}" class="h-full w-full object-cover">
                            @endif
                        </div>

                        <div class="mt-4 space-y-2">
                            <p class="text-xs uppercase tracking-[0.16em] text-slate-500">{{ $recentProduct->category->name ?? 'Uncategorized' }}</p>
                            <h3 class="text-lg font-semibold text-slate-900">{{ $recentProduct->name }}</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-base font-semibold text-slate-900">INR {{ number_format($recentProduct->price, 2) }}</span>
                                <a href="{{ route('user.products.show', $recentProduct) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50">View</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
