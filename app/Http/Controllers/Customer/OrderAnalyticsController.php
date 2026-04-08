<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\OrderAnalyticsService;
use Illuminate\View\View;

class OrderAnalyticsController extends Controller
{
    public function __construct(protected OrderAnalyticsService $orderAnalyticsService)
    {
    }

    public function index(): View
    {
        return view('user.orders.analytics', $this->orderAnalyticsService->getAnalyticsForUser(auth()->user()));
    }
}
