<?php

namespace App\Http\Controllers\Customer;

use App\Events\ProductReviewed;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreReviewRequest;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request, Product $product): RedirectResponse
    {
        $user = $request->user();

        if (! $this->hasPurchasedProduct((int) $user->id, (int) $product->id)) {
            return back()->with('error', 'You can review only products you have purchased.');
        }

        Review::updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id' => $user->id,
            ],
            [
                'rating' => (int) $request->validated('rating'),
                'comment' => $request->validated('comment'),
                'is_approved' => false,
            ]
        );

        event(new ProductReviewed($product->fresh()));

        return back()->with('success', 'Your review has been submitted for approval.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $review = Review::query()
            ->where('product_id', $product->id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $review->delete();

        event(new ProductReviewed($product->fresh()));

        return back()->with('success', 'Your review has been removed.');
    }

    private function hasPurchasedProduct(int $userId, int $productId): bool
    {
        return OrderItem::query()
            ->where('product_id', $productId)
            ->whereHas('order', function ($query) use ($userId) {
                $query
                    ->where('user_id', $userId)
                    ->where('status', '!=', 'cancelled');
            })
            ->exists();
    }
}
