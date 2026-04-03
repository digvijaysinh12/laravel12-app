<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\User;

class GenerateAdminReport extends Command
{
    protected $signature = 'report:admin {--type=sales} {--format=csv}';

    protected $description = 'Generate admin reports';

    public function handle()
    {
        $type = $this->option('type');
        $format = $this->option('format');

        if (!in_array($type, ['sales', 'inventory', 'customers'])) {
            $this->error('Invalid type');
            return;
        }

        if (!in_array($format, ['csv', 'json'])) {
            $this->error('Invalid format');
            return;
        }

        $this->info("Generating {$type} report...");

        $this->withProgressBar(range(1, 50), function () {
            usleep(20000);
        });

        $data = $this->getReportData($type);

        $fileName = "{$type}_" . time() . ".{$format}";
        $path = "reports/{$fileName}";

        if ($format === 'json') {
            Storage::put($path, json_encode($data, JSON_PRETTY_PRINT));
        } else {
            Storage::put($path, $this->toCsv($data));
        }

        $this->newLine(2);
        $this->info("Saved: storage/app/{$path}");

        $this->table(array_keys($data[0] ?? []), $data);
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