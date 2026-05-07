<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AdminNotification::latest()
            ->take(20)
            ->get();

        return response()->json($notifications);
    }

    public function unread()
    {
        $notifications = AdminNotification::where('is_read', false)
            ->latest()
            ->get();

        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $notification = AdminNotification::findOrFail($id);

        $notification->update([
            'is_read' => true,
        ]);

        Cache::forget('unread_notifications_count');

        return response()->json([
            'success' => true,
        ]);
    }

    public function markAllRead()
    {
        AdminNotification::where('is_read', false)
            ->update([
                'is_read' => true,
            ]);

        Cache::forget('unread_notifications_count');

        return response()->json([
            'success' => true,
        ]);
    }
}
