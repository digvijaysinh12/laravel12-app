<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily sales report at 2 AM
Schedule::command('report:generate sales json')->dailyAt('02:00');

// Stock check every day at 8 AM
Schedule::command('stock:check')->dailyAt('08:00');

// Clean old logs weekly (suday 1 AM)
Schedule::command('log:clean')->weekly()->sundays()->at('01:00');

// CUSTOMER TASKS

// Send cart reminders daily at 10 AM
Schedule::command('cart')->dailyAt('10:00');

// Update order status hourly
Schedule::command('orders:update-status')->hourly();

// Send birthday emails daily at 9 AM
Schedule::command('birthday:emails')->dailyAt('09:00');


Schedule::command('promotion:send') // you already have this
    ->dailyAt('09:00');
