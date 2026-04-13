<div class="grid gap-4 md:grid-cols-2">

    <!-- CLEAR ALL -->
    <x-admin.card title="Clear All Cache" description="Flush entire cache storage.">
        <form method="POST" action="{{ route('admin.cache.clear') }}"
              onsubmit="return confirm('Are you sure you want to clear all cache?')">
            @csrf
            <x-admin.button type="submit" variant="danger">
                🧹 Clear All Cache
            </x-admin.button>
        </form>
    </x-admin.card>

    <!-- CLEAR TAG -->
    <x-admin.card title="Clear Cache by Tag" description="Flush specific tag (Redis only).">

        <form method="POST" action="{{ route('admin.cache.clearTag', 'products') }}"
              onsubmit="return confirm('Clear products cache?')">
            @csrf
            <x-admin.button type="submit">
                Clear Products Cache
            </x-admin.button>
        </form>

        <form method="POST" action="{{ route('admin.cache.clearTag', 'admin') }}"
              class="mt-2"
              onsubmit="return confirm('Clear admin cache?')">
            @csrf
            <x-admin.button type="submit">
                Clear Admin Cache
            </x-admin.button>
        </form>

        <form method="POST" action="{{ route('admin.cache.clearTag', 'customer') }}"
              class="mt-2"
              onsubmit="return confirm('Clear customer cache?')">
            @csrf
            <x-admin.button type="submit">
                Clear Customer Cache
            </x-admin.button>
        </form>

        @if(config('cache.default') !== 'redis')
            <p class="text-xs text-red-500 mt-2">
                ⚠️ Tag clearing requires Redis
            </p>
        @endif

    </x-admin.card>

</div>