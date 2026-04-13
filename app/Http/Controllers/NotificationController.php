<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Notification::query()->latest();

        if ($user && $user->role === 'admin') {
            // FIXED: admin sees global notifications.
            $query->whereNull('user_id');
        } elseif ($user) {
            // FIXED: user sees only their own notifications.
            $query->where('user_id', $user->id);
        }

        return response()->json(
            $query->take(20)->get(['id', 'title', 'message', 'is_read', 'created_at'])
        );
    }

    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $notification = Notification::query()->where('id', $id);

        if ($user && $user->role === 'admin') {
            $notification->whereNull('user_id');
        } elseif ($user) {
            $notification->where('user_id', $user->id);
        }

        $row = $notification->firstOrFail();
        $row->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Notification::query();

        if ($user && $user->role === 'admin') {
            $query->whereNull('user_id');
        } elseif ($user) {
            $query->where('user_id', $user->id);
        }

        $query->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
