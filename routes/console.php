<?php

use App\Services\CancelPrepurchase;
use App\Services\RetryFailedAuthSri;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Every fifteen minutes, retry authorization for pending SRI invoices
 */
Schedule::call(function() {
    RetryFailedAuthSri::retry();
    CancelPrepurchase::cancel();
})->everyFifteenMinutes();