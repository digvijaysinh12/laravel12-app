@extends('admin.layouts.app')

@section('page-title', 'Manual Alert')

@section('content')
    <div class="max-w-3xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-slate-900">Send Admin Alert</h2>
            <p class="mt-1 text-sm text-slate-500">
                This form uses Laravel on-demand notifications so no separate alert system is introduced.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.notifications.manual-alert.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="recipient_email" class="mb-2 block text-sm font-medium text-slate-700">Recipient Email</label>
                <input
                    id="recipient_email"
                    name="recipient_email"
                    type="email"
                    value="{{ old('recipient_email') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none"
                    required
                >
                @error('recipient_email')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="subject" class="mb-2 block text-sm font-medium text-slate-700">Subject</label>
                <input
                    id="subject"
                    name="subject"
                    type="text"
                    value="{{ old('subject') }}"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none"
                    required
                >
                @error('subject')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="message" class="mb-2 block text-sm font-medium text-slate-700">Message</label>
                <textarea
                    id="message"
                    name="message"
                    rows="8"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-slate-500 focus:outline-none"
                    required
                >{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-medium text-white">
                    Queue Alert
                </button>
            </div>
        </form>
    </div>
@endsection
