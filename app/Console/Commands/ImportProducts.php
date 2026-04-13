<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import product from csv file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = (string) $this->argument('file');

        if (! is_file($filePath)) {
            $this->error('File not found');

            return self::FAILURE;
        }

        $totalRows = $this->countDataRows($filePath);

        if ($totalRows === 0) {
            $this->warn('No data rows found');

            return self::SUCCESS;
        }

        $this->info('Starting import...');

        $created = 0;
        $updated = 0;
        $errors = 0;
        $progressBar = $this->output->createProgressBar($totalRows);
        $progressBar->start();

        foreach ($this->readRows($filePath)->chunk(500) as $chunk) {
            foreach ($chunk as $lineNumber => $row) {
                try {
                    $validator = Validator::make($row, [
                        'name' => ['required', 'string', 'max:255'],
                        'price' => ['required', 'numeric', 'min:0'],
                        'description' => ['nullable', 'string'],
                        'category_id' => ['nullable', 'integer', 'exists:categories,id'],
                        'stock' => ['nullable', 'integer', 'min:0'],
                        'image' => ['nullable', 'string', 'max:255'],
                        'is_featured' => ['nullable', 'boolean'],
                    ]);

                    if ($validator->fails()) {
                        $errors++;
                        $this->warn('Row '.$lineNumber.' skipped: '.implode(', ', $validator->errors()->all()));
                        continue;
                    }

                    $validated = $validator->validated();
                    $payload = array_filter([
                        'price' => (float) $validated['price'],
                        'description' => $validated['description'] ?? null,
                        'category_id' => $validated['category_id'] ?? null,
                        'stock' => isset($validated['stock']) ? (int) $validated['stock'] : 0,
                        'image' => $validated['image'] ?? null,
                        'is_featured' => isset($validated['is_featured']) ? (bool) $validated['is_featured'] : false,
                    ], static fn ($value) => $value !== null && $value !== '');

                    $product = Product::updateOrCreate(
                        ['name' => $validated['name']],
                        $payload
                    );

                    if ($product->wasRecentlyCreated) {
                        $created++;
                    } else {
                        $updated++;
                    }
                } catch (Throwable $e) {
                    $errors++;
                    $this->warn('Row '.$lineNumber.' skipped: '.$e->getMessage());
                } finally {
                    $progressBar->advance();
                }
            }
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info("Import complete. Created: {$created}, Updated: {$updated}, Errors: {$errors}");

        return self::SUCCESS;
    }

    private function readRows(string $filePath): LazyCollection
    {
        return LazyCollection::make(function () use ($filePath) {
            $handle = fopen($filePath, 'r');

            if ($handle === false) {
                return;
            }

            $headers = fgetcsv($handle);

            if ($headers === false) {
                fclose($handle);

                return;
            }

            $headers = array_map(
                static fn ($header) => strtolower(trim((string) $header)),
                $headers
            );

            $lineNumber = 1;

            while (($row = fgetcsv($handle)) !== false) {
                $lineNumber++;
                $normalizedRow = array_map(
                    static fn ($value) => is_string($value) ? trim($value) : $value,
                    $row
                );

                yield $lineNumber => array_combine(
                    $headers,
                    array_pad($normalizedRow, count($headers), null)
                ) ?: [];
            }

            fclose($handle);
        });
    }

    private function countDataRows(string $filePath): int
    {
        $file = new \SplFileObject($filePath, 'r');
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY);
        $file->seek(PHP_INT_MAX);

        return max(0, $file->key());
    }
}
