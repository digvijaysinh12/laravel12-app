<x-app-layout>
    <div class="mx-auto max-w-4xl space-y-4 px-4 py-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold text-slate-900">{{ __('notifications.title') }}</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ __('notifications.latest_updates') }}</p>
                </div>
                @if($hasUnreadNotifications)
                    <form method="POST" action="{{ route('notifications.readAll') }}">
                        @csrf
                        <button type="submit" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                            {{ __('notifications.mark_all_read') }}
                        </button>
                    </form>
                @endif
            </div>

            <div class="space-y-3">
                @forelse($notifications as $notification)
                    <x-notifications.item
                        :notification="$notification"
                        :wrapper-class="'flex w-full items-start justify-between gap-4 rounded-2xl border border-slate-200 px-5 py-4 text-left transition hover:border-slate-300 hover:bg-slate-50'"
                    />
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 px-6 py-10 text-center text-sm text-slate-500">
                        {{ __('notifications.empty') }}
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
