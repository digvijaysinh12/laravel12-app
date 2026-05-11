<?php

namespace App\Http\Controllers;

use App\Support\Notifications\NotificationCache;
use App\Support\Notifications\NotificationViewData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationViewData $notificationViewData,
    ) {}

    /**
     * Show notifications as either HTML or JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();
        if ($request->expectsJson()) {
            $limit = min((int) $request->integer('limit', 10), 50);

            return response()->json($this->notificationViewData->forUser($user, $limit)['latestNotifications']);
        }

        $notifications = $this->notificationViewData->paginateForUser($user);
        $hasUnreadNotifications = collect($notifications->items())
            ->contains(fn (array $notification): bool => ! ($notification['is_read'] ?? false));

        return view('notifications.index', compact('notifications', 'hasUnreadNotifications'));
    }

    /**
     * Return unread notifications only.
     */
    public function unread(Request $request): JsonResponse
    {
        return response()->json(
            $this->notificationViewData->unreadForUser($request->user())
        );
    }

    /**
     * Mark single notification as read.
     */
    public function markAsRead(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        $notification = $this->notificationViewData
            ->queryFor($user)
            ->findOrFail($id);

        $notification->markAsRead();

        NotificationCache::forgetFor($request->user());

        $redirectUrl = $notification->data['action_url']
            ?? $this->fallbackRedirectUrl($request, $notification);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'redirect_url' => $redirectUrl,
            ], Response::HTTP_OK);
        }

        return redirect()->to($redirectUrl);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllRead(Request $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        $user->unreadNotifications()
            ->where('data->audience', $this->notificationViewData->audienceFor($user))
            ->get()
            ->markAsRead();

        NotificationCache::forgetFor($request->user());

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
            ], Response::HTTP_OK);
        }

        return back()->with(
            'success',
            'All notifications marked as read.'
        );
    }

    private function fallbackRedirectUrl(Request $request, DatabaseNotification $notification): string
    {
        if (isset($notification->data['order_id'])) {
            return $request->user()->role === 'admin'
                ? route('admin.orders.show', $notification->data['order_id'])
                : route('user.orders.show', $notification->data['order_id']);
        }

        if (isset($notification->data['product_id']) && $request->user()->role === 'admin') {
            return route('admin.products.edit', $notification->data['product_id']);
        }

        return url()->previous() ?: route('notifications.index');
    }
}
