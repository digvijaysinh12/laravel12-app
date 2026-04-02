<?php

namespace App\Console\Commands;

use App\Services\Reports\ReportService;
use Illuminate\Console\Command;
use Throwable;

class GenerateAdminReport extends Command
{
    protected $signature = 'report:generate {type} {format} {--days=30}';

    protected $description = 'Generate admin reports (sales | inventory | customers) in csv/json/pdf';

    public function handle(ReportService $reportService)
    {
        $type = strtolower($this->argument('type'));
        $format = strtolower($this->argument('format'));
        $days = (int) $this->option('days');

        if (! $reportService->isValidType($type)) {
            $this->error('Invalid type. Allowed: sales, inventory, customers');
            return Command::FAILURE;
        }

        if (! $reportService->isValidFormat($format)) {
            $this->error('Invalid format. Allowed: csv, json, pdf');
            return Command::FAILURE;
        }

        try {
            $this->info("Generating {$type} report ({$format}) for last {$days} days...");

            $total = $reportService->totalRecords($type, $days);
            $this->output->progressStart(max(1, $total));

            $report = $reportService->generate($type, $days, function (int $step = 1) {
                $this->output->progressAdvance($step);
            });

            $this->output->progressFinish();

            $path = $reportService->store($type, $format, $report);

            $this->newLine();
            $this->info("Saved: storage/app/{$path}");

            // Console summaries
            $this->table($report['summary_headers'], $report['summary_rows']);

            if (! empty($report['details_rows'])) {
                $this->newLine();
                $this->table($report['details_headers'], $report['details_rows']);
            }

            // Optional email hook
            $reportService->maybeEmail($path, $report['meta']);

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->output->progressFinish();
            $this->error('Report generation failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
