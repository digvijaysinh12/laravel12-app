<div
    id="{{ $id }}"
    x-cloak
    x-transition.opacity
    @click.self="open = false"
    {{ $attributes->merge([
        'class' => 'fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 p-4'
    ]) }}
    role="dialog"
    aria-modal="true"
>

    <!-- Modal Box -->
    <div
        class="w-full max-w-lg rounded-xl bg-white shadow-xl transform transition-all"
        x-transition.scale
    >

        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <h3 class="text-lg font-medium text-slate-900">
                {{ $title }}
            </h3>

            <button
                type="button"
                class="text-slate-400 hover:text-slate-700 text-xl leading-none"
                @click="open = false"
            >
                &times;
            </button>
        </div>

        <!-- Body -->
        <div class="px-5 py-4 text-sm text-slate-600">
            {{ $slot }}
        </div>

    </div>
</div>