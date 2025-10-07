<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArmadaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepoControllers;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\RouteOptimizationControllers;
use App\Http\Controllers\TpsController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', fn() => view('auth.login'));
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes (Admin & Pengguna)
Route::middleware(['auth', 'role:admin,pengguna'])->group(function () {
    Route::get('/rute', [PetugasController::class, 'rute'])->name('petugas.rute');

    // VRP API (untuk komputasi optimasi rute)
    Route::post('/api/run-vrp', [RouteOptimizationControllers::class, 'runVRP']);

    // Dashboard Admin
    Route::prefix('dashboard-admin')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.admin');
        Route::get('/data-tps', [AdminController::class, 'dataTps'])->name('admin.tps');
        Route::get('/data-depo', [AdminController::class, 'dataDepo'])->name('admin.depo');
        Route::get('/data-armada', [AdminController::class, 'dataArmada'])->name('admin.armada');
        Route::get('/optimasi-rute', [AdminController::class, 'optimasiRute'])->name('admin.optimasi');
    });
});

// Admin Only Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Armada
    Route::get('/armadas', [ArmadaController::class, 'index']);
    Route::get('/armadas/create', [ArmadaController::class, 'create'])->name('armadas.create');
    Route::post('/armadas', [ArmadaController::class, 'store'])->name('armadas.store');
    Route::get('/armadas/{id}/edit', [ArmadaController::class, 'edit'])->name('armadas.edit');
    Route::put('/armadas/{id}', [ArmadaController::class, 'update'])->name('armadas.update');
    Route::delete('/armadas/{id}', [ArmadaController::class, 'destroy'])->name('armadas.destroy');

    // Depo
    Route::post('/depos', [DepoControllers::class, 'store']);
    Route::put('/depos/{id}', [DepoControllers::class, 'update']);
    Route::delete('/depos/{id}', [DepoControllers::class, 'destroy']);

    // TPS
    Route::get('/tps/create', [TpsController::class, 'create'])->name('tps.create');
    Route::post('/tps', [TpsController::class, 'store'])->name('tps.store');
    Route::get('/tps/{id}/edit', [TpsController::class, 'edit'])->name('tps.edit');
    Route::put('/tps/{id}', [TpsController::class, 'update'])->name('tps.update');
    Route::delete('/tps/{id}', [TpsController::class, 'destroy'])->name('tps.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});
