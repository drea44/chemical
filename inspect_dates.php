<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

foreach (['2026-03', '2026-04', '2026-05', '2026-06', '2026-07'] as $m) {
    $dates = \App\Models\ChemicalLogDate::where('period_month', $m)->orderBy('log_date')->orderBy('id')->get();
    echo "=== Period $m ===\n";
    foreach ($dates as $d) {
        $uCount = \App\Models\ChemicalDailyUsage::where('log_date_id', $d->id)->count();
        echo "  ID: {$d->id} | Date: {$d->log_date->format('Y-m-d')} | Analyst: {$d->analyst_name} | Usages: {$uCount}\n";
    }
}
