<x-guest-layout>
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Login</h1>
        <p class="mt-2 text-sm text-slate-600">
            Sign in to continue shopping.
        </p>

        <x-auth-session-status class="mt-4" :status="session('status')" />

        @if ($errors->any())
            <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input
                    id="email"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                    class="mt-1.5"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="password" value="Password" />
                <x-text-input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="mt-1.5"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <div class="flex items-center justify-between text-sm">
                <label for="remember_me" class="inline-flex items-center gap-2 text-slate-600">
                    <input
                        id="remember_me"
                        type="checkbox"
                        class="rounded border-slate-300 text-slate-900 focus:ring-slate-500"
                        name="remember"
                    >
                    Remember me
                </label>

                <a href="{{ route('password.request') }}" class="font-medium text-slate-700 hover:text-slate-900">
                    Forgot Password?
                </a>
            </div>

            <x-button type="submit" class="w-full">Login</x-button>
        </form>

        <p class="mt-6 text-sm text-slate-600">
            New here?
            <a href="{{ route('register') }}" class="font-semibold text-slate-900 hover:underline">
                Create Account
            </a>
        </p>
    </div>
</x-guest-layout>
