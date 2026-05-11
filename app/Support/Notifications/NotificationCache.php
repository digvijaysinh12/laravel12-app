<?php

namespace App\Support\Notifications;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;

class NotificationCache
{
    public static function unreadCountKey(int|string $userId, string $audience): string
    {
        return 'unread_notifications_count_'.$audience.'_'.$userId;
    }

    public static function unreadCountForAudience(Authenticatable|User $user, string $audience): int
    {
        return Cache::remember(
            self::unreadCountKey($user->getAuthIdentifier(), $audience),
            60,
            fn (): int => $user->unreadNotifications()
                ->where('data->audience', $audience)
                ->count(),
        );
    }

    public static function forgetFor(Authenticatable|User $user): void
    {
        foreach (['admin', 'customer'] as $audience) {
            Cache::forget(self::unreadCountKey($user->getAuthIdentifier(), $audience));
        }
    }
}
