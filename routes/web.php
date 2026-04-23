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

Route::patch('/dashboard/drivers/{user}', [DashboardController::class, 'updateDriver'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.drivers.update');

Route::delete('/dashboard/drivers/{user}', [DashboardController::class, 'destroyDriver'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.drivers.destroy');

Route::post('/dashboard/units', [DashboardController::class, 'storeUnit'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.units.store');

Route::patch('/dashboard/units/{unit}', [DashboardController::class, 'updateUnit'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.units.update');

Route::delete('/dashboard/units/{unit}', [DashboardController::class, 'destroyUnit'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.units.destroy');

Route::get('/dashboard/manage', [DashboardController::class, 'manageResources'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.manage.index');

Route::post('/dashboard/assignments', [DashboardController::class, 'assignDriver'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.assignments.store');

Route::get('/dashboard/assignments', [DashboardController::class, 'assignments'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.assignments.index');

Route::get('/dashboard/menus', [DashboardController::class, 'menus'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.menus.index');

Route::post('/dashboard/menus', [DashboardController::class, 'storeMenu'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.menus.store');

Route::patch('/dashboard/menus/{menu}', [DashboardController::class, 'updateMenu'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.menus.update');

Route::delete('/dashboard/menus/{menu}', [DashboardController::class, 'destroyMenu'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.menus.destroy');

Route::patch('/dashboard/assignments/{assignment}/finish', [DashboardController::class, 'finishAssignment'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.assignments.finish');

Route::post('/dashboard/driver/attendance/clock-in', [DashboardController::class, 'driverClockIn'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.driver.attendance.clock-in');

Route::post('/dashboard/driver/attendance/clock-out', [DashboardController::class, 'driverClockOut'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.driver.attendance.clock-out');

Route::get('/dashboard/driver/attendance/qr', [DashboardController::class, 'driverAttendanceViaQr'])
    ->middleware(['auth', 'verified', 'signed:relative'])
    ->name('dashboard.driver.attendance.qr');

Route::get('/dashboard/driver/products', [DashboardController::class, 'driverProducts'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.driver.products.index');

Route::put('/dashboard/driver/products', [DashboardController::class, 'updateDriverProducts'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.driver.products.update');

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
