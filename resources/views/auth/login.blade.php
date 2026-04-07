<x-guest-layout>
    <div class="grid gap-0 overflow-hidden rounded-[2rem] bg-white shadow-xl ring-1 ring-slate-200 lg:grid-cols-[0.95fr_1.05fr]">
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-700 p-8 text-white sm:p-10">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-300">Welcome back</p>
            <h1 class="mt-4 text-3xl font-semibold tracking-tight">Sign in to your account</h1>
            <p class="mt-4 text-sm leading-7 text-slate-300">
                Access your dashboard, manage your cart, and continue where you left off.
            </p>
        </div>

        <div class="p-8 sm:p-10">
            <h2 class="text-2xl font-semibold tracking-tight text-slate-900">Login</h2>
            <p class="mt-2 text-sm text-slate-500">Use your registered email and password to continue.</p>

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                </div>

                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="current-password" />
                </div>

                <div class="flex items-center justify-between gap-3 text-sm">
                    <label for="remember_me" class="inline-flex items-center gap-2 text-slate-600">
                        <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-slate-950 shadow-sm focus:ring-slate-500" name="remember">
                        Remember me
                    </label>

                    <a href="{{ route('password.request') }}" class="font-medium text-slate-700 transition hover:text-slate-950">
                        Forgot password?
                    </a>
                </div>

                <x-button type="submit" class="w-full">Log in</x-button>
            </form>

            <p class="mt-6 text-sm text-slate-500">
                Don’t have an account?
                <a href="{{ route('register') }}" class="font-medium text-slate-900 transition hover:underline">Register here</a>
            </p>
        </div>
    </div>
</x-guest-layout>
