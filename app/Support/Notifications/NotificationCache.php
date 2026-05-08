<?php

namespace App\Support\Notifications;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;

class NotificationCache
{
    public static function unreadCountKey(int|string $userId): string
    {
        return 'unread_notifications_count_'.$userId;
    }

    public static function unreadCountFor(Authenticatable|User $user): int
    {
        return Cache::remember(
            self::unreadCountKey($user->getAuthIdentifier()),
            60,
            fn (): int => $user->unreadNotifications()->count(),
        );
    }

    public static function forgetFor(Authenticatable|User $user): void
    {
        Cache::forget(self::unreadCountKey($user->getAuthIdentifier()));
    }
}
