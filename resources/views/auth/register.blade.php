<x-guest-layout>
    <div class="grid gap-0 overflow-hidden rounded-[2rem] bg-white shadow-xl ring-1 ring-slate-200 lg:grid-cols-[0.95fr_1.05fr]">
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-700 p-8 text-white sm:p-10">
            <p class="text-xs uppercase tracking-[0.3em] text-slate-300">Join us</p>
            <h1 class="mt-4 text-3xl font-semibold tracking-tight">Create your account</h1>
            <p class="mt-4 text-sm leading-7 text-slate-300">
                Register to shop products, track your cart, and receive invoices instantly.
            </p>
        </div>

        <div class="p-8 sm:p-10">
            <h2 class="text-2xl font-semibold tracking-tight text-slate-900">Register</h2>
            <p class="mt-2 text-sm text-slate-500">Fill in the details below to create a new account.</p>

            <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <x-input-label for="name" value="Name" />
                    <x-text-input id="name" class="mt-2 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                </div>

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                </div>

                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="new-password" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="Confirm Password" />
                    <x-text-input id="password_confirmation" class="mt-2 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                </div>

                <x-button type="submit" class="w-full">Create account</x-button>
            </form>

            <p class="mt-6 text-sm text-slate-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-slate-900 transition hover:underline">Sign in here</a>
            </p>
        </div>
    </div>
</x-guest-layout>
