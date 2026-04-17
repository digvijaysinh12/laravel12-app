@extends('admin.layouts.app')

@section('page-title', 'Reviews')

@section('content')
<div class="space-y-6">
    <x-admin.card title="Review Moderation" description="Approve customer reviews before they appear on product pages.">
        <div class="mb-4 flex flex-wrap gap-2">
            <x-admin.button href="{{ route('admin.reviews.index', ['status' => 'all']) }}" variant="{{ $status === 'all' ? 'primary' : 'secondary' }}">All</x-admin.button>
            <x-admin.button href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" variant="{{ $status === 'pending' ? 'primary' : 'secondary' }}">Pending</x-admin.button>
            <x-admin.button href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" variant="{{ $status === 'approved' ? 'primary' : 'secondary' }}">Approved</x-admin.button>
        </div>

        <x-admin.table :headers="['Product', 'Customer', 'Rating', 'Comment', 'Status', 'Action']">
            @forelse ($reviews as $review)
                <tr class="align-top hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-900">{{ $review->product->name ?? 'Deleted product' }}</td>
                    <td class="px-4 py-3 text-slate-600">
                        <p>{{ $review->user->name ?? 'Unknown' }}</p>
                        <p class="text-xs text-slate-400">{{ $review->user->email ?? '' }}</p>
                    </td>
                    <td class="px-4 py-3 text-slate-700">{{ $review->rating }}/5</td>
                    <td class="px-4 py-3 text-slate-600">{{ $review->comment ?: 'No comment provided.' }}</td>
                    <td class="px-4 py-3">
                        <x-admin.badge :tone="$review->is_approved ? 'success' : 'warning'">
                            {{ $review->is_approved ? 'Approved' : 'Pending' }}
                        </x-admin.badge>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_approved" value="{{ $review->is_approved ? 0 : 1 }}">
                                <x-admin.button type="submit" variant="secondary" class="px-3 py-1.5 text-xs">
                                    {{ $review->is_approved ? 'Unapprove' : 'Approve' }}
                                </x-admin.button>
                            </form>

                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review?');">
                                @csrf
                                @method('DELETE')
                                <x-admin.button type="submit" variant="danger" class="px-3 py-1.5 text-xs">Delete</x-admin.button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-500">No reviews found for this filter.</td>
                </tr>
            @endforelse
        </x-admin.table>

        <div class="mt-4">
            {{ $reviews->links() }}
        </div>
    </x-admin.card>
</div>
@endsection
