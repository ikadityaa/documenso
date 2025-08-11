<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

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