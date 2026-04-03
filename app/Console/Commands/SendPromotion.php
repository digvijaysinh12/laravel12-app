<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SendPromotion extends Command
{
    protected $signature = 'promotion:send';
    protected $description = 'Send promotional emails';

    public function handle()
    {
        // Ask inputs
        $code = $this->ask('Enter discount code');

        $percentage = $this->ask('Enter discount percentage (0-100)');

        if ($percentage < 0 || $percentage > 100) {
            return $this->error('Invalid percentage! Must be between 0 and 100.');
        }

        // Audience selection
        $audience = $this->choice(
            'Select target audience',
            ['all', 'new_customers', 'inactive', 'top_buyers'],
            0
        );

        // Get users (based on audience)
        $users = $this->getAudienceUsers($audience);

        if ($users->isEmpty()) {
            return $this->warn('No users found for selected audience.');
        }
        // Preview
        $this->info('Preview:');
        $this->table(
            ['Field', 'Value'],
            [
                ['Code', $code],
                ['Discount', $percentage . '%'],
                ['Audience', $audience],
                ['Total Users', $users->count()],
            ]
        );

        // Confirm
        if (!$this->confirm('Do you want to send this promotion?')) {
            return $this->warn('Operation cancelled.');
        }

        // Simulate sending
        $this->info('Sending emails...');

        $this->withProgressBar($users, function ($user) use ($code, $percentage) {
            
            $this->line("Sending to : {$user->email}");
            usleep(50000);
        });

        $this->newLine(2);
        $this->info('Promotion emails sent successfully!');
    }

    // Audience logic
    private function getAudienceUsers($audience)
    {
        return match ($audience) {
            'all' => User::where('role', 'user')->get(),

            'new_customers' => User::where('role', 'user')
                ->whereDate('created_at', today())
                ->get(),

            'inactive' => User::where('role', 'user')
                ->whereDoesntHave('orders') 
                ->get(),

            'top_buyers' => User::where('role', 'user')
                ->withCount('orders')
                ->orderBy('orders_count', 'desc')
                ->take(5)
                ->get(),

            default => collect(),
        };
    }
}