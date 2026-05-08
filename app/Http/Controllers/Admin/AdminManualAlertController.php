<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminAlertRequest;
use App\Notifications\AdminManualAlert;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminManualAlertController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {
    }

    public function create(): View
    {
        return view('admin.notifications.manual-alert');
    }

    public function store(StoreAdminAlertRequest $request): RedirectResponse
    {
        $payload = $request->validated();

        $this->notificationService->sendOnDemand(
            $payload['recipient_email'],
            new AdminManualAlert($payload['subject'], $payload['message']),
            [
                'actor_id' => $request->user()?->id,
                'subject' => $payload['subject'],
            ],
        );

        return back()->with('success', 'Admin alert queued successfully.');
    }
}
