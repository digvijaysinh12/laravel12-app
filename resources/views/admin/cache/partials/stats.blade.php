<div class="grid gap-4 md:grid-cols-3">

    <x-admin.card title="Hit Rate">
        <div class="text-2xl font-bold text-green-600">
            {{ $stats['hit_rate'] }}
        </div>
    </x-admin.card>

    <x-admin.card title="Total Cache Keys">
        <div class="text-2xl font-bold text-blue-600">
            {{ $stats['total_keys'] }}
        </div>
    </x-admin.card>

    <x-admin.card title="Cache Size">
        <div class="text-2xl font-bold text-purple-600">
            {{ $stats['cache_size'] }}
        </div>
    </x-admin.card>

</div>