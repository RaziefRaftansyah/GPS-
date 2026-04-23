<?php

use App\Http\Controllers\Dashboard\AssignmentController;
use App\Http\Controllers\Dashboard\AttendanceController;
use App\Http\Controllers\Dashboard\DriverProductController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\MenuController;
use App\Http\Controllers\Dashboard\ResourceController;
use App\Http\Controllers\Dashboard\TraccarController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LocationController::class, 'index'])->name('tracker.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/users/{user}/kick', [HomeController::class, 'kickUser'])->name('dashboard.users.kick');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/manage', [ResourceController::class, 'index'])->name('manage.index');
        Route::post('/drivers', [ResourceController::class, 'storeDriver'])->name('drivers.store');
        Route::patch('/drivers/{user}', [ResourceController::class, 'updateDriver'])->name('drivers.update');
        Route::delete('/drivers/{user}', [ResourceController::class, 'destroyDriver'])->name('drivers.destroy');
        Route::post('/units', [ResourceController::class, 'storeUnit'])->name('units.store');
        Route::patch('/units/{unit}', [ResourceController::class, 'updateUnit'])->name('units.update');
        Route::delete('/units/{unit}', [ResourceController::class, 'destroyUnit'])->name('units.destroy');

        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::patch('/assignments/{assignment}/finish', [AssignmentController::class, 'finish'])->name('assignments.finish');

        Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
        Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
        Route::patch('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');

        Route::post('/driver/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('driver.attendance.clock-in');
        Route::post('/driver/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('driver.attendance.clock-out');
        Route::get('/driver/attendance/qr', [AttendanceController::class, 'viaQr'])
            ->middleware('signed:relative')
            ->name('driver.attendance.qr');

        Route::get('/driver/products', [DriverProductController::class, 'index'])->name('driver.products.index');
        Route::put('/driver/products', [DriverProductController::class, 'update'])->name('driver.products.update');

        Route::get('/traccar', [TraccarController::class, 'index'])->name('traccar');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/deactivate', [ProfileController::class, 'deactivate'])->name('profile.deactivate');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
