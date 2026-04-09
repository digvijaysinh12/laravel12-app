<x-guest-layout>
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Forgot Password</h1>
        <p class="mt-2 text-sm text-slate-600">We will email you a password reset link.</p>

        <x-auth-session-status class="mt-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus class="mt-1.5" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <x-button type="submit" class="w-full">Send Reset Link</x-button>
        </form>
    </div>
</x-guest-layout>
