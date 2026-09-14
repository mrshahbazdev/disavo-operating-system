<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::registerCommand(app(\App\Domains\Graph\Commands\BackfillKnowledgeEdgesCommand::class));
Artisan::registerCommand(app(\App\Domains\Graph\Commands\DetectOrphansCommand::class));

