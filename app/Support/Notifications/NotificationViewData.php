<?php

namespace App\Support\Notifications;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class NotificationViewData
{
    public function forUser(User $user, int $limit = 10): array
    {
        $audience = $this->audienceFor($user);
        $query = $this->queryFor($user, $audience);

        return [
            'audience' => $audience,
            'unreadCount' => NotificationCache::unreadCountForAudience($user, $audience),
            'latestNotifications' => $query
                ->take($limit)
                ->get()
                ->map(fn ($notification): array => NotificationPayload::fromDatabaseNotification($notification))
                ->all(),
        ];
    }

    public function paginateForUser(User $user, int $perPage = 20): LengthAwarePaginator
    {
        $audience = $this->audienceFor($user);

        $paginator = $this->queryFor($user, $audience)->paginate($perPage);

        $paginator->through(fn ($notification): array => NotificationPayload::fromDatabaseNotification($notification));

        return $paginator;
    }

    public function unreadForUser(User $user): array
    {
        $audience = $this->audienceFor($user);

        return $user->unreadNotifications()
            ->where('data->audience', $audience)
            ->latest()
            ->get()
            ->map(fn ($notification): array => NotificationPayload::fromDatabaseNotification($notification))
            ->all();
    }

    public function audienceFor(User $user): string
    {
        return $user->role === 'admin'
            ? 'admin'
            : 'customer';
    }

    public function queryFor(User $user, ?string $audience = null): MorphMany
    {
        return $user->notifications()
            ->where('data->audience', $audience ?? $this->audienceFor($user))
            ->latest();
    }
}
