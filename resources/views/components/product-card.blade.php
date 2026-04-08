@php
    $stock = (int) ($product->stock ?? 0);
@endphp

<article class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <a href="{{ auth()->check() ? route('user.products.show', $product->id) : route('login') }}" class="block bg-slate-50">
        @if ($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-52 w-full object-cover">
        @else
            <div class="flex h-52 items-center justify-center text-sm text-slate-500">No image available</div>
        @endif
    </a>

    <div class="flex flex-1 flex-col gap-4 p-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $product->category->name ?? 'Uncategorized' }}</p>
            <h3 class="mt-1 text-base font-semibold text-slate-900">{{ $product->name }}</h3>
            <p class="mt-2 text-sm text-slate-600">{{ $product->description ?: 'No description available.' }}</p>
        </div>

        <div class="mt-auto space-y-3">
            <div class="flex items-center justify-between gap-3">
                <span class="text-lg font-semibold text-slate-900">INR {{ number_format($product->price, 2) }}</span>
                <span class="product-stock-badge inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $stock > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}" data-product-id="{{ $product->id }}">
                    {{ $stock > 0 ? 'In Stock' : 'Out of Stock' }}
                </span>
            </div>

            <div class="flex flex-col gap-2">
                <a href="{{ auth()->check() ? route('user.products.show', $product->id) : route('login') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    View Details
                </a>

                @if (auth()->check() && auth()->user()->role === 'user')
                    <form action="{{ route('user.cart.add', $product->id) }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="add-to-cart-btn inline-flex w-full items-center justify-center rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                            data-product-id="{{ $product->id }}"
                            @disabled($stock <= 0)
                        >
                            {{ $stock <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</article>
