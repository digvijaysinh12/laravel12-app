<x-guest-layout>
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Reset Password</h1>
        <p class="mt-2 text-sm text-slate-600">Choose a new password for your account.</p>

        <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" class="mt-1.5" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="password" value="New Password" />
                <x-text-input id="password" type="password" name="password" required autocomplete="new-password" class="mt-1.5" />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Confirm Password" />
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="mt-1.5" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
            </div>

            <x-button type="submit" class="w-full">Reset Password</x-button>
        </form>
    </div>
</x-guest-layout>
