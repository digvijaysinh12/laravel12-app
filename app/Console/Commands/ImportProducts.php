<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\LazyCollection;

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
    public function handle()
    {
        $filePath = $this->argument('file');

        if(!file_exists($filePath)){
            $this->error('File not found');
            return;
        }

        $this->info('Starting import...');

        $rows = LazyCollection::make(function() use ($filePath){
            $handle = fopen($filePath, 'r');

            while(($row= fgetcsv($handle))!==false){
                yield $row;
            }

            fclose($handle);
        });
    }
}
