<?php

namespace App\Services\Reports;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    private array $types = ['sales', 'inventory', 'customers'];
    private array $formats = ['csv', 'json', 'pdf'];

    public function __construct(
        private SalesReportService $sales,
        private InventoryReportService $inventory,
        private CustomerReportService $customers,
    ) {
    }

    public function isValidType(string $type): bool
    {
        return in_array($type, $this->types, true);
    }

    public function isValidFormat(string $format): bool
    {
        return in_array($format, $this->formats, true);
    }

    public function totalRecords(string $type, int $days): int
    {
        return match ($type) {
            'sales' => $this->sales->count($days),
            'inventory' => $this->inventory->count(),
            'customers' => $this->customers->count($days),
        };
    }

    public function generate(string $type, int $days, callable $progress): array
    {
        return match ($type) {
            'sales' => $this->sales->generate($days, $progress),
            'inventory' => $this->inventory->generate($progress),
            'customers' => $this->customers->generate($days, $progress),
        };
    }

    public function store(string $type, string $format, array $report): string
    {
        $dir = "reports/{$type}";
        Storage::makeDirectory($dir);

        $timestamp = now()->format('Ymd_His');
        $filename = "{$type}_report_{$timestamp}.{$format}";
        $path = "$dir/$filename";

        $payload = [
            'meta' => $report['meta'],
            'summary' => $report['summary_rows'],
            'details' => $report['details_rows'] ?? [],
        ];

        match ($format) {
            'json' => Storage::put($path, json_encode($payload, JSON_PRETTY_PRINT)),
            'csv' => Storage::put($path, $this->toCsv($report)),
            'pdf' => Storage::put($path, $this->renderPdf($report)),
        };

        return $path;
    }

    public function maybeEmail(string $path, array $meta): void
    {
        // Hook for mailing reports; keep silent if not configured.
        // Example: Mail::to(config('reports.to'))->queue(new ReportMail($path, $meta));
    }

    private function toCsv(array $report): string
    {
        $stream = fopen('php://temp', 'r+');

        fputcsv($stream, ['Title', $report['meta']['title']]);
        fputcsv($stream, ['Generated At', $report['meta']['generated_at']]);
        fputcsv($stream, ['Filters', $report['meta']['filters'] ?? '-']);
        fputcsv($stream, []);

        fputcsv($stream, $report['summary_headers']);
        foreach ($report['summary_rows'] as $row) {
            fputcsv($stream, array_values($row));
        }

        if (!empty($report['details_rows'])) {
            fputcsv($stream, []);
            fputcsv($stream, $report['details_headers']);
            foreach ($report['details_rows'] as $row) {
                fputcsv($stream, array_values($row));
            }
        }

        rewind($stream);
        return stream_get_contents($stream);
    }

    private function renderPdf(array $report): string
    {
        $pdf = Pdf::loadView('reports.report', ['report' => $report]);
        return $pdf->output();
    }
}
