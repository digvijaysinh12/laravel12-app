<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Support\Notifications\NotificationCache;
use App\Support\Notifications\NotificationPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    /**
     * Show notifications as either HTML or JSON.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = $request->user()
            ->notifications()
            ->latest();

        if ($request->expectsJson()) {
            $limit = min((int) $request->integer('limit', 10), 50);

            return response()->json(
                $query->take($limit)->get()->map(
                    fn (DatabaseNotification $notification): array => NotificationPayload::fromDatabaseNotification($notification)
                )->all()
            );
        }

        $notifications = $query->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Return unread notifications only.
     */
    public function unread(Request $request): JsonResponse
    {
        $notifications = $request->user()
            ->unreadNotifications()
            ->latest()
            ->get();

        return response()->json(
            $notifications->map(
                fn (DatabaseNotification $notification): array => NotificationPayload::fromDatabaseNotification($notification)
            )->all()
        );
    }

    /**
     * Mark single notification as read.
     */
    public function markAsRead(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $notification = $request->user()
            ->notifications()
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
        $request->user()
            ->unreadNotifications
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
