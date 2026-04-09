<x-guest-layout>
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Verify Email</h1>
        <p class="mt-2 text-sm text-slate-600">Please verify your email address before continuing.</p>

        <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
            @csrf
            <x-primary-button>
                Resend Verification Email
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <x-button type="submit">Log Out</x-button>
        </form>
    </div>
</x-guest-layout>
