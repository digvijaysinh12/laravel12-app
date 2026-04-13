<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService)
    {
    }

    public function index(): View
    {
        LogHelper::log('Admin dashboard viewed');

        return view('admin.dashboard.index', $this->dashboardService->getDashboardData());
    }
}
