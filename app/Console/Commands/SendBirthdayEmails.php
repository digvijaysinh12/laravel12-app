<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendBirthdayEmails extends Command
{
    protected $signature = 'birthday:emails';

    protected $description = 'Send birthday emails';

    public function handle(): int
    {
        $this->info('Sending birthday emails...');

        return self::SUCCESS;
    }
}
