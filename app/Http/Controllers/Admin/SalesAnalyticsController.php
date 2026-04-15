<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SalesAnalyticsService;
use App\Services\Reports\ReportService;
use App\Services\Reports\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $files = Storage::disk('reports')->files();
        $files = collect($files)
            ->sortByDesc(fn($file) => Storage::disk('reports')->lastModified($file))
            ->values();
        $analytics = $this->salesAnalyticsService->getAnalytics();
        return view('admin.reports.index', array_merge($analytics, [
            'files' => $files,
        ]));
    }

    public function export()
    {
        $type = request('type', 'sales');
        $format = request('format', 'csv');

        $reportService = app(ReportService::class);

        $file = $reportService->generate($type, $format);

        return response()->json([
            'success' => true,
            'message' => 'Report generated successfully!',
            'tone' => 'success',
            'download_url' => route('admin.reports.download', $file),
        ]);
    }
}
