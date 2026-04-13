<?php

namespace App\Console\Commands;

use App\Services\Reports\ReportService;
use Illuminate\Console\Command;

class GenerateAdminReport extends Command
{
    protected $signature = 'report:admin {--type=sales} {--format=csv}';
    protected $description = 'Generate an admin report';

    public function handle(): int
    {
        try {
            // Read simple options from the command line.
            $type = $this->option('type') ?: 'sales';
            $format = $this->option('format') ?: 'csv';

            $reportService = new ReportService();
            $filePath = '';

            // Keep the progress bar simple and beginner-friendly.
            $steps = ['prepare', 'generate', 'finish'];

            $this->withProgressBar($steps, function ($step) use (&$filePath, $reportService, $type, $format) {
                if ($step === 'generate') {
                    $filePath = $reportService->generate($type, $format);
                }

                usleep(50000);
            });

            $this->newLine(2);

            $this->table(
                ['Field', 'Value'],
                [
                    ['Type', $type],
                    ['Format', $format],
                    ['Saved To', $filePath],
                ]
            );

            $this->info('Report generated successfully.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
