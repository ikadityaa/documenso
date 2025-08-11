<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\SessionController;
use App\Http\Middleware\EnsureInstalled;
use Illuminate\Support\Facades\Route;

// Installer routes
Route::prefix('install')->group(function () {
    Route::get('/', [InstallationController::class, 'welcome'])->name('install.welcome');
    Route::get('/requirements', [InstallationController::class, 'requirements'])->name('install.requirements');
    Route::get('/environment', [InstallationController::class, 'environment'])->name('install.environment');
    Route::post('/environment', [InstallationController::class, 'saveEnvironment'])->name('install.environment.save');
    Route::get('/admin', [InstallationController::class, 'admin'])->name('install.admin');
    Route::post('/admin', [InstallationController::class, 'saveAdmin'])->name('install.admin.save');
    Route::get('/finish', [InstallationController::class, 'finish'])->name('install.finish');
});

// Protect the application until installed
Route::middleware([EnsureInstalled::class])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        // Admin routes
        Route::middleware(['role:admin|super-admin'])->group(function () {
            Route::resource('sessions', SessionController::class);
            Route::get('sessions/{session}/qr', [QRController::class, 'show'])->name('sessions.qr');
            Route::post('sessions/{session}/qr/refresh', [QRController::class, 'regenerate'])->name('sessions.qr.refresh');

            Route::get('sessions/{session}/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
            Route::get('sessions/{session}/attendance/export', [AttendanceController::class, 'exportCsv'])->name('attendance.export');
        });

        // Student scan endpoint (requires auth; login redirects back)
        Route::get('qr/scan', [QRController::class, 'scan'])->name('qr.scan');
    });

    require __DIR__.'/auth.php';
}
);