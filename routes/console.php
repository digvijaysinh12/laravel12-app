<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily sales report at 2 AM
Schedule::command('report:admin --type=sales --format=json')->dailyAt('02:00');

// Stock check every day at 8 AM
Schedule::command('stock:check')->dailyAt('08:00');

// Clean old logs weekly
Schedule::command('logs:clean')->weekly();

// CUSTOMER TASKS

// Send cart reminders daily at 10 AM
Schedule::command('cart:reminder')->dailyAt('10:00');

// Update order status hourly
Schedule::command('order:update-status')->hourly();

// Send birthday emails daily at 9 AM
Schedule::command('birthday:emails')->dailyAt('09:00');
