<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



Artisan::command('queue:run-once', function () {
    $this->comment("Running queue worker once...");

    Artisan::call('queue:work', [
        '--once' => true,
        '--queue' => 'default',
    ]);

    $this->info("Queue cycle completed!");
})->purpose('Run the queue worker one time safely');