<?php

namespace App\Services\Reports;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;

class ReportService
{
    public function generate($type, $format, $days = 30)
    {
        $type = strtolower($type);
        $format = strtolower($format);

        if (!in_array($type, ['sales', 'inventory', 'customers'], true)) {
            throw new InvalidArgumentException('Invalid report type');
        }

        if (!in_array($format, ['csv', 'json', 'pdf'], true)) {
            throw new InvalidArgumentException('Invalid format');
        }

        $data = match ($type) {
            'sales' => (new SalesReportService())->getData($days),
            'inventory' => (new InventoryReportService())->getData(),
            'customers' => (new CustomerReportService())->getData(),
        };

        if (empty($data)) {
            throw new RuntimeException('Report data is empty');
        }

        return $this->store($type, $format, $data);
    }

    public function store($type, $format, $data)
    {
        // Save files in storage/app/reports.
        $disk = Storage::disk('local');
        $fileName = $type.'_'.now()->format('Ymd_His').'.'.$format;
        $path = 'reports/'.$fileName;
        $rows = $this->normalizeRows($data);

        if ($format === 'json') {
            $disk->put($path, json_encode($data, JSON_PRETTY_PRINT));
        }

        if ($format === 'csv') {
            $stream = fopen('php://temp', 'w+');
            $headers = array_keys($rows[0] ?? []);

            fputcsv($stream, $headers);

            foreach ($rows as $row) {
                fputcsv($stream, array_values($row));
            }

            rewind($stream);
            $disk->put($path, stream_get_contents($stream));
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.report', [
                'type' => $type,
                'headers' => array_keys($rows[0] ?? []),
                'rows' => $rows,
            ]);

            $disk->put($path, $pdf->output());
        }

        return $disk->path($path);
    }

    private function normalizeRows(array $data): array
    {
        if (array_is_list($data)) {
            return $data;
        }

        return [$data];
    }
}
