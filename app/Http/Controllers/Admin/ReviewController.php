<?php

namespace App\Http\Controllers\Admin;

use App\Events\ProductReviewed;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');

        $reviews = Review::query()
            ->with(['product:id,name', 'user:id,name,email'])
            ->when($status === 'pending', fn ($query) => $query->where('is_approved', false))
            ->when($status === 'approved', fn ($query) => $query->where('is_approved', true))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'status'));
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'is_approved' => ['required', 'boolean'],
        ]);

        $review->update([
            'is_approved' => (bool) $validated['is_approved'],
        ]);

        event(new ProductReviewed($review->product()->firstOrFail()));

        return back()->with('success', 'Review status updated successfully.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $product = $review->product()->first();
        $review->delete();

        if ($product) {
            event(new ProductReviewed($product));
        }

        return back()->with('success', 'Review deleted successfully.');
    }
}
