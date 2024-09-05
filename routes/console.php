<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

if (config('backup.enabled')) {
    Schedule::command('backup:clean')->timezone('Asia/Bangkok')->daily()->at('01:00');
    Schedule::command('backup:run')->timezone('Asia/Bangkok')->daily()->at('01:30');
}

