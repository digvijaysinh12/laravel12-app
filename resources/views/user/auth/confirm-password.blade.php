<x-guest-layout>
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Confirm Password</h1>
        <p class="mt-2 text-sm text-slate-600">This action requires your password to continue.</p>

        <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" type="password" name="password" required autocomplete="current-password" class="mt-1.5" />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <x-primary-button>
                Confirm
            </x-primary-button>
        </form>
    </div>
</x-guest-layout>
