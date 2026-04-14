<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LocationController::class, 'index'])->name('tracker.index');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::post('/dashboard/users/{user}/kick', [DashboardController::class, 'kickUser'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.users.kick');

Route::post('/dashboard/drivers', [DashboardController::class, 'storeDriver'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.drivers.store');

Route::post('/dashboard/units', [DashboardController::class, 'storeUnit'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.units.store');

Route::post('/dashboard/assignments', [DashboardController::class, 'assignDriver'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.assignments.store');

Route::patch('/dashboard/assignments/{assignment}/finish', [DashboardController::class, 'finishAssignment'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.assignments.finish');

Route::get('/dashboard/traccar', [DashboardController::class, 'traccar'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.traccar');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/deactivate', [ProfileController::class, 'deactivate'])->name('profile.deactivate');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
