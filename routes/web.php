<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChemicalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\QRScannerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ─── Guest Routes ────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── Authenticated Routes ────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Redirect root to dashboard
    Route::redirect('/', '/dashboard');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Chemical Registry & Label Printing
    Route::get('/chemicals/{chemical}/label', [ChemicalController::class, 'printLabel'])->name('chemicals.label');
    Route::resource('chemicals', ChemicalController::class);

    // Master Data
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers',  SupplierController::class);
    Route::resource('locations',  LocationController::class);

    // Stock Management
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/',           [StockController::class, 'index'])->name('index');
        Route::get('/in',         [StockController::class, 'showStockIn'])->name('in');
        Route::post('/in',        [StockController::class, 'processStockIn'])->name('in.process');
        Route::get('/out',        [StockController::class, 'showStockOut'])->name('out');
        Route::post('/out',       [StockController::class, 'processStockOut'])->name('out.process');
        Route::get('/adjustment', [StockController::class, 'showAdjustment'])->name('adjustment');
        Route::post('/adjustment',[StockController::class, 'processAdjustment'])->name('adjustment.process');
    });

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // QR Scanner
    Route::get('/qr-scanner',        [QRScannerController::class, 'index'])->name('qr-scanner.index');
    Route::post('/qr-scanner/scan',  [QRScannerController::class, 'scan'])->name('qr-scanner.scan');

    // Reports
    Route::get('/reports',           [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv',[ReportController::class, 'exportCsv'])->name('reports.export-csv');

    // Audit Trail
    Route::get('/audit-trail', [AuditTrailController::class, 'index'])->name('audit-trail.index');

    // User Management
    Route::resource('users', UserController::class)->except(['destroy']);
    Route::post('/users/{user}/deactivate',     [UserController::class, 'deactivate'])->name('users.deactivate');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Settings
    Route::get('/settings',  [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings',  [SettingsController::class, 'update'])->name('settings.update');
});
