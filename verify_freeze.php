<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$session = $app->make('session')->driver();
$session->setId('test-session-2');
$session->start();
$session->put($app->make('auth')->guard()->getName(), $user->getAuthIdentifier());
$session->save();

echo "=== VERIFYING FREEZE PANE & DATES PER MONTH ===\n\n";

$months = [
    '2026-03' => 31,
    '2026-04' => 30,
    '2026-05' => 31,
    '2026-06' => 30,
    '2026-07' => 31,
    '2026-09' => 30,
];

foreach ($months as $m => $expectedDays) {
    $request = Illuminate\Http\Request::create('/transactions?month=' . $m, 'GET');
    $request->setLaravelSession($session);
    $request->setUserResolver(fn() => $user);
    $response = $httpKernel->handle($request);
    
    $html = $response->getContent();
    $status = $response->getStatusCode();
    
    $dateTakenCount = substr_count($html, 'Date Taken');
    $stickyNoCount = substr_count($html, 'sticky-col-no');
    $stickyNameCount = substr_count($html, 'sticky-col-name');
    $stickyPengeluaranCount = substr_count($html, 'sticky-col-pengeluaran');
    
    echo "Month: {$m} (Expected days: {$expectedDays})\n";
    echo "  HTTP Status: {$status}\n";
    echo "  'Date Taken' headers rendered: {$dateTakenCount}\n";
    echo "  Sticky 'No' cells: {$stickyNoCount}\n";
    echo "  Sticky 'Chemical Name' cells: {$stickyNameCount}\n";
    echo "  Sticky 'Pengeluaran' divider cells: {$stickyPengeluaranCount}\n";
    echo "  Has scroll container: " . (str_contains($html, 'freeze-scroll-container') ? 'YES' : 'NO') . "\n";
    echo "----------------------------------------\n";
}
