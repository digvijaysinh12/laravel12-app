<?php

namespace App\Services\Reports;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;

class ReportService
{
    public function generate($type,$format,$days){

        $type = strtolower($type);
        $format = strtolower($format);

        if(!in_array($type,['sales','inventory','customer'],true)){
            throw new InvalidArgumentException('Invalid report type');
        }

        if(!in_array($format,['csv','json','pdf'],true)){
            throw new InvalidArgumentException("Invalid format");
        }

        $data = match($type){
            'sales' => (new SalesReportService())->getData($days),
            'inventory' => (new InventoryReportService())->getData(),
            'customer' => (new CustomerReportService())->getData(),
        };

        if(empty($data)){
            throw new RuntimeException('Report data is empty');
        }
        return $this->store($type, $format, $data);
    }

public function store($type, $format, $data)
{
    $disk = Storage::disk('public'); // or config('filesystems.default')
    $fileName = "{$type}_" . now()->format('Ymd_His') . ".{$format}";
    $path = "reports/{$type}/{$fileName}";

    if ($format === 'json') {
        $disk->put($path, json_encode($data, JSON_PRETTY_PRINT));
    }

    if ($format === 'csv') {
        $stream = fopen('php://temp', 'w+');
        fputcsv($stream, array_keys($data[0]));
        foreach ($data as $row) {
            fputcsv($stream, $row);
        }
        rewind($stream);
        $disk->put($path, stream_get_contents($stream));
    }

    if ($format === 'pdf') {
        $pdf = Pdf::loadView('reports.report', ['data' => $data]);
        $disk->put($path, $pdf->output());
    }

    return $disk->path($path);
}

}
