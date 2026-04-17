@php
    $existingItems = collect(old('items', isset($order) ? $order->items->map(fn ($item) => [
        'product_id' => $item->product_id,
        'quantity' => $item->quantity,
    ])->all() : [
        ['product_id' => '', 'quantity' => 1],
    ]));

    if ($existingItems->isEmpty()) {
        $existingItems = collect([
            ['product_id' => '', 'quantity' => 1],
        ]);
    }

    $customerOptions = $customers->mapWithKeys(fn ($customer) => [
        $customer->id => $customer->name.' ('.$customer->email.')',
    ])->all();
@endphp

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div class="grid gap-4 md:grid-cols-2">
        <x-admin.select
            name="user_id"
            label="Customer"
            :options="$customerOptions"
            :selected="$order->user_id ?? ''"
            placeholder="Select customer"
            required
        />

        <x-admin.input
            name="phone"
            label="Phone"
            :value="$order->phone ?? ''"
            placeholder="Enter customer phone"
            required
        />

        <x-admin.select
            name="status"
            label="Order Status"
            :options="$statuses"
            :selected="$order->status ?? 'pending'"
            required
        />

        <x-admin.select
            name="payment_status"
            label="Payment Status"
            :options="$paymentStatuses"
            :selected="$order->payment_status ?? 'pending'"
            required
        />

        <x-admin.select
            name="payment_method"
            label="Payment Method"
            :options="$paymentMethods"
            :selected="$order->payment_method ?? 'COD'"
            required
        />

        <div class="space-y-1.5 md:col-span-2">
            <label for="shipping_address" class="block text-sm font-medium text-slate-700">Shipping Address</label>
            <textarea
                id="shipping_address"
                name="shipping_address"
                rows="4"
                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                required
            >{{ old('shipping_address', $order->shipping_address ?? '') }}</textarea>
            @error('shipping_address')
                <p class="text-xs text-rose-600">{{ $message }}</p>
            @else
                <p class="text-xs text-transparent">.</p>
            @enderror
        </div>
    </div>

    <section class="rounded-xl border bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Order Items</h2>
                <p class="mt-1 text-sm text-slate-500">Choose products and quantities for this order.</p>
            </div>

            <x-admin.button type="button" id="add-order-item" variant="secondary">Add Item</x-admin.button>
        </div>

        <div id="order-items" class="mt-5 space-y-4">
            @foreach ($existingItems as $index => $item)
                <div class="order-item grid gap-4 rounded-xl border border-slate-200 p-4 md:grid-cols-[1fr_180px_auto]">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-slate-700">Product</label>
                        <select
                            name="items[{{ $index }}][product_id]"
                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                            required
                        >
                            <option value="">Select product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected((string) ($item['product_id'] ?? '') === (string) $product->id)>
                                    {{ $product->name }} | INR {{ number_format($product->price, 2) }} | Stock {{ $product->stock }}
                                </option>
                            @endforeach
                        </select>
                        @error("items.$index.product_id")
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @else
                            <p class="text-xs text-transparent">.</p>
                        @enderror
                    </div>

                    <x-admin.input
                        :name="'items['.$index.'][quantity]'"
                        label="Quantity"
                        type="number"
                        :value="$item['quantity'] ?? 1"
                        min="1"
                        required
                    />

                    <div class="flex items-end">
                        <button type="button" class="remove-order-item inline-flex w-full items-center justify-center rounded-lg border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-medium text-rose-700 transition hover:bg-rose-100">
                            Remove
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        @error('items')
            <p class="mt-3 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </section>

    <div class="flex flex-wrap justify-end gap-3">
        <x-admin.button href="{{ route('admin.orders.index') }}" variant="secondary">Cancel</x-admin.button>
        <x-admin.button type="submit">{{ $submitLabel }}</x-admin.button>
    </div>
</form>

<template id="order-item-template">
    <div class="order-item grid gap-4 rounded-xl border border-slate-200 p-4 md:grid-cols-[1fr_180px_auto]">
        <div class="space-y-1.5">
            <label class="block text-sm font-medium text-slate-700">Product</label>
            <select
                data-field="product"
                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                required
            >
                <option value="">Select product</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}">
                        {{ $product->name }} | INR {{ number_format($product->price, 2) }} | Stock {{ $product->stock }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-transparent">.</p>
        </div>

        <div class="space-y-1.5">
            <label class="block text-sm font-medium text-slate-700">Quantity</label>
            <input
                data-field="quantity"
                type="number"
                min="1"
                value="1"
                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-slate-200"
                required
            >
            <p class="text-xs text-transparent">.</p>
        </div>

        <div class="flex items-end">
            <button type="button" class="remove-order-item inline-flex w-full items-center justify-center rounded-lg border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-medium text-rose-700 transition hover:bg-rose-100">
                Remove
            </button>
        </div>
    </div>
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const itemsContainer = document.getElementById('order-items');
    const template = document.getElementById('order-item-template');
    const addButton = document.getElementById('add-order-item');

    if (!itemsContainer || !template || !addButton) {
        return;
    }

    const refreshIndexes = () => {
        itemsContainer.querySelectorAll('.order-item').forEach((row, index) => {
            row.querySelector('[data-field="product"], select[name*="[product_id]"]')?.setAttribute('name', `items[${index}][product_id]`);
            row.querySelector('[data-field="quantity"], input[name*="[quantity]"]')?.setAttribute('name', `items[${index}][quantity]`);
        });
    };

    addButton.addEventListener('click', () => {
        const fragment = template.content.cloneNode(true);
        itemsContainer.appendChild(fragment);
        refreshIndexes();
    });

    itemsContainer.addEventListener('click', (event) => {
        const button = event.target.closest('.remove-order-item');

        if (!button) {
            return;
        }

        if (itemsContainer.querySelectorAll('.order-item').length === 1) {
            return;
        }

        button.closest('.order-item')?.remove();
        refreshIndexes();
    });

    refreshIndexes();
});
</script>
@endpush
