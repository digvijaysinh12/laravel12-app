<?php

namespace App\Console\Commands;

use App\Services\Reports\ReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\User;

class GenerateAdminReport extends Command
{
    protected $signature = 'report:generate {type} {format} {--days=30}';
    protected $description = 'Generate admin reports';

    public function handle()
    {
        $type = $this->argument('type');
        $format = $this->argument('format');
        $days = $this->option('days');

        $this->info("Generating $type report in $format format...");

        $reportService = new ReportService();

        $filePath = $reportService->generate($type,$format,$days);

        $this->info("Report generated: " . $filePath);
    }

    private function getReportData($type)
    {
        return match ($type) {
            'sales' => $this->salesReport(),
            'inventory' => $this->inventoryReport(),
            'customers' => $this->customerReport(),
        };
    }

    private function salesReport()
    {
        $totalProducts = Product::count();
        $totalValue = Product::sum('price');
        $topProduct = Product::orderByDesc('price')->first()?->name ?? 'N/A';

        return [
            [
                'Total Products' => $totalProducts,
                'Total Value' => $totalValue,
                'Top Product' => $topProduct,
            ]
        ];
    }

    private function inventoryReport()
    {
        return [
            [
                'Low Stock' => Product::where('stock', '<', 10)->count(),
                'Out Of Stock' => Product::where('stock', 0)->count(),
                'Total Value' => Product::sum('price'),
            ]
        ];
    }

    private function customerReport()
    {
        return [
            [
                'New Users Today' => User::whereDate('created_at', today())->count(),
                'Total Users' => User::count(),
                'Admins' => User::where('role', 'admin')->count(),
            ]
        ];
    }

    private function toCsv($data)
    {
        $output = fopen('php://temp', 'r+');

        fputcsv($output, array_keys($data[0]));

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        return stream_get_contents($output);
    }
}