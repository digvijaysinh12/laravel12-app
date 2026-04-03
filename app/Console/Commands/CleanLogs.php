<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old log files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info(" Cleaning logs... ");

        $path = storage_path('logs');
        $files = File::files($path);

        $deleted =0;

        foreach($files as $file){
            if(now()->diffInDays(\Carbon\Carbon::createFromTimestamp($file->getCTime()))>7){
                File::delete($file->getRealPath());
                $deleted++;
            }
        }

        $this->info("Deleted $deleted old logs files.");
    }
}
