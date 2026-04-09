@extends('user.layouts.app')

@section('title', 'Profile')

@section('content')
<div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Profile information</h1>
        <p class="mt-1 text-sm text-slate-600">Update the name and email address tied to your account.</p>

        <form method="POST" action="{{ route('user.profile.update') }}" class="mt-6 space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-400">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-400">
            </div>

            <div class="flex items-center justify-between gap-4">
                <p class="text-sm text-slate-500">
                    Role: <span class="font-medium text-slate-900">{{ $user->role }}</span>
                </p>
                <button type="submit" class="inline-flex items-center rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700">
                    Save Changes
                </button>
            </div>
        </form>
    </section>

    <div class="space-y-6">
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Account Actions</h2>
            <p class="mt-2 text-sm text-slate-600">Deleting your account will sign you out and remove your profile.</p>

            <form method="POST" action="{{ route('user.profile.destroy') }}" class="mt-5 space-y-4">
                @csrf
                @method('DELETE')

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Confirm Password</label>
                    <input type="password" name="password" required class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-slate-400">
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg border border-rose-300 bg-rose-50 px-4 py-2.5 text-sm font-medium text-rose-700 transition hover:bg-rose-100">
                    Delete Account
                </button>
            </form>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Account Summary</h2>
            <div class="mt-4 space-y-3 text-sm text-slate-600">
                <div class="flex items-center justify-between gap-4">
                    <span>Name</span>
                    <span class="font-medium text-slate-900">{{ $user->name }}</span>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <span>Email</span>
                    <span class="font-medium text-slate-900">{{ $user->email }}</span>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <span>Role</span>
                    <span class="font-medium text-slate-900">{{ $user->role }}</span>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
