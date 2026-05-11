<?php

namespace App\View\Components\User;

use App\Support\Notifications\NotificationViewData;
use Illuminate\View\Component;
use Illuminate\View\View;

class Navbar extends Component
{
    public function __construct(
        private readonly NotificationViewData $notificationViewData,
    ) {
    }

    public function render(): View
    {
        $notificationData = auth()->check()
            ? $this->notificationViewData->forUser(auth()->user())
            : [
                'audience' => 'customer',
                'unreadCount' => 0,
                'latestNotifications' => [],
            ];

        return view('components.navbar.customer-navbar', [
            'notificationAudience' => $notificationData['audience'],
            'notificationUnreadCount' => $notificationData['unreadCount'],
            'latestNotifications' => $notificationData['latestNotifications'],
        ]);
    }
}
