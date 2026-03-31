<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('report:admin --type=sales')->dailyAt('02:00');

Schedule::command('report:admin --type=inventory')->dailyAt('08:00');

Schedule::command('log:clear')->weekly();

Schedule::command('promotion:send')->dailyAt('10:00');

Schedule::command('orders:update-status')->hourly();

Schedule::command('birthday:emails')->dailyAt('09:00');
