<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SalesAnalyticsService;
use App\Services\Reports\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesAnalyticsController extends Controller
{
    public function __construct(
        protected SalesAnalyticsService $salesAnalyticsService,
        protected SalesReportService $salesReportService
    ) {
    }

    public function index(): View
    {
        return view('admin.reports.index', $this->salesAnalyticsService->getAnalytics());
    }

    public function export(Request $request): StreamedResponse
    {
        return $this->salesReportService->exportCsv((int) $request->query('days', 30));
    }
}
