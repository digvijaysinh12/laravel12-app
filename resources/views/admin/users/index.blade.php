@extends('layouts.admin')

@section('page-title', 'Users')

@section('content')
<div class="space-y-6">
    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-xs uppercase tracking-[0.24em] text-slate-500">User management</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-slate-900">Users</h1>
        <p class="mt-2 text-sm text-slate-500">A reusable view for future user administration features.</p>
    </section>

    <x-card title="User directory">
        @if(isset($users) && count($users))
            <x-table :headers="['#', 'Name', 'Email', 'Role']">
                @foreach($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-3 py-4 text-slate-500">{{ $user->id }}</td>
                        <td class="px-3 py-4 font-medium text-slate-900">{{ $user->name }}</td>
                        <td class="px-3 py-4 text-slate-600">{{ $user->email }}</td>
                        <td class="px-3 py-4">
                            <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ $user->role }}</span>
                        </td>
                    </tr>
                @endforeach
            </x-table>
        @else
            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-16 text-center">
                <h2 class="text-xl font-semibold text-slate-900">No users loaded yet</h2>
                <p class="mt-2 text-sm text-slate-500">This screen is ready for a user controller or future admin tools.</p>
            </div>
        @endif
    </x-card>
</div>
@endsection
