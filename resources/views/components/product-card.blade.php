@php
    $stock = (int) ($product->stock ?? 0);
    $rating = number_format((float) data_get($product, 'rating', 4.5), 1);
    $discountPercent = (int) data_get($product, 'discount_percentage', 0);
    $originalPrice = (float) data_get($product, 'original_price', 0);

    if ($discountPercent <= 0 && $originalPrice > $product->price && $originalPrice > 0) {
        $discountPercent = (int) round((($originalPrice - $product->price) / $originalPrice) * 100);
    }

    if ($originalPrice <= 0 && $discountPercent > 0 && $discountPercent < 100) {
        $originalPrice = $product->price / (1 - ($discountPercent / 100));
    }
@endphp

<div class="group flex h-full flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    <div class="relative overflow-hidden bg-slate-100">
        @if ($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" class="h-52 w-full object-cover transition duration-300 group-hover:scale-[1.02]" alt="{{ $product->name }}">
        @else
            <div class="flex h-52 items-center justify-center text-sm text-slate-500">No image available</div>
        @endif

        @if ($discountPercent > 0)
            <span class="absolute left-3 top-3 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                {{ $discountPercent }}% OFF
            </span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-4">
        <p class="text-xs font-medium uppercase tracking-[0.08em] text-slate-500">{{ $product->category->name ?? 'Uncategorized' }}</p>
        <h3 class="mt-1 text-base font-semibold text-slate-900">{{ $product->name }}</h3>

        <div class="mt-2 flex items-center gap-1 text-xs text-amber-500">
            <span>*</span>
            <span>*</span>
            <span>*</span>
            <span>*</span>
            <span class="text-slate-400">*</span>
            <span class="ml-1 font-medium text-slate-600">{{ $rating }}</span>
        </div>

        <div class="mt-3 flex items-end gap-2">
            <span class="text-lg font-semibold text-slate-900">INR {{ number_format($product->price, 2) }}</span>
            @if ($discountPercent > 0)
                <span class="text-sm text-slate-400 line-through">INR {{ number_format($originalPrice, 2) }}</span>
            @endif
        </div>

        <div class="mt-3">
            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $stock > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                {{ $stock > 0 ? 'In Stock' : 'Out of Stock' }}
            </span>
        </div>

        <div class="mt-4 grid gap-2">
            <a
                href="{{ route('user.products.show', $product->id) }}"
                class="inline-flex w-full items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
            >
                View Details
            </a>

            @if(auth()->check() && auth()->user()->role === 'user')
                <form action="{{ route('user.cart.add', $product->id) }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-black disabled:cursor-not-allowed disabled:bg-slate-300"
                        @disabled($stock <= 0)
                    >
                        {{ $stock <= 0 ? 'Out of Stock' : 'Add to Cart' }}
                    </button>
                </form>
            @endif

            @if(auth()->check() && auth()->user()->role === 'admin')
                <a
                    href="{{ route('admin.products.edit', $product->id) }}"
                    class="inline-flex w-full items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-black"
                >
                    Edit Product
                </a>
            @endif
        </div>
    </div>
</div>
