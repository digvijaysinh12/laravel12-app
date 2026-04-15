<?php

namespace App\Services\Reports;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;

class ReportService
{
    public function generate(string $type, string $format, int $days = 30): string
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
            'sales' => app(SalesReportService::class)->getData($days),
            'inventory' => app(InventoryReportService::class)->getData(),
            'customers' => app(CustomerReportService::class)->getData(),
        };

        if (empty($data)) {
            throw new RuntimeException('Report data is empty');
        }

        return $this->store($type, $format, $data);
    }

    private function store(string $type, string $format, array $data): string
    {
        $disk = Storage::disk('reports');

        $fileName = "{$type}-report-" . now()->format('Ymd_His') . ".{$format}";
        $rows = $this->normalizeRows($data);

        if ($format === 'json') {
            $disk->put($fileName, json_encode($data, JSON_PRETTY_PRINT));
        }

        if ($format === 'csv') {
            $stream = fopen('php://temp', 'w+');

            $headers = array_keys($rows[0] ?? []);
            fputcsv($stream, $headers);

            foreach ($rows as $row) {
                fputcsv($stream, array_values($row));
            }

            rewind($stream);
            $disk->put($fileName, stream_get_contents($stream));
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.report', [
                'type' => $type,
                'headers' => array_keys($rows[0] ?? []),
                'rows' => $rows,
            ]);

            $disk->put($fileName, $pdf->output());
        }

        return $fileName; 
    }

    private function normalizeRows(array $data): array
    {
        return array_is_list($data) ? $data : [$data];
    }
}