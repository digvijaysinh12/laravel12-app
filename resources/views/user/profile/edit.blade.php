@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
    <x-card title="Profile information">
        <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-400 focus:bg-white">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-400 focus:bg-white">
            </div>

            <div class="flex items-center justify-between gap-4">
                <p class="text-sm text-slate-500">
                    Role: <span class="font-medium text-slate-900">{{ $user->role }}</span>
                </p>
                <x-button type="submit">Save Changes</x-button>
            </div>
        </form>
    </x-card>

    <div class="space-y-6">
        <x-card title="Account actions">
            <form method="POST" action="{{ route('user.profile.destroy') }}" class="space-y-4">
                @csrf
                @method('DELETE')

                <p class="text-sm text-slate-500">Deleting your account removes your profile and signs you out.</p>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Confirm Password</label>
                    <input type="password" name="password" required
                        class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-slate-400 focus:bg-white">
                </div>

                <x-button type="submit" variant="danger" class="w-full">Delete Account</x-button>
            </form>
        </x-card>

        <x-card title="Account summary">
            <div class="space-y-3 text-sm text-slate-600">
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
        </x-card>
    </div>
</div>
@endsection
