<div
    id="{{ $id }}"
    {{ $attributes->merge(['class' => 'fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 p-4']) }}
    x-cloak
    role="dialog"
    aria-modal="true"
>
    <div class="w-full max-w-lg rounded-xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <h3 class="text-lg font-medium text-slate-900">{{ $title }}</h3>
            <button type="button" class="text-slate-400 hover:text-slate-700" @click="open = false">&times;</button>
        </div>
        <div class="px-5 py-4">
            {{ $slot }}
        </div>
    </div>
</div>
