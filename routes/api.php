<?php

use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

Route::post('/location', [LocationController::class, 'store'])->name('api.location.store');
Route::get('/location/latest', [LocationController::class, 'latest'])->name('api.location.latest');
Route::get('/location/test', [LocationController::class, 'browserTest'])->name('api.location.test');
Route::get('/location/test-send', [LocationController::class, 'browserTestSend'])->name('api.location.test-send');
