<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\RequestController;



Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () {
    Route::get('/dashboard', function () {
        return view('programacion');
    })->name('dashboard');
});
Route::get('/playlist/create', [PlaylistController::class, 'create'])->name('playlist.create');
Route::get('/playlists', [PlaylistController::class, 'index'])->name('playlist.index');
Route::get('/playlist/{playlist}', [PlaylistController::class, 'show'])->name('playlist.show');
Route::post('/playlist/store', [PlaylistController::class, 'store'])->name('playlist.store');

Route::get('/request/create', [RequestController::class, 'index'])->name('request.create');
Route::post('/request/store', [RequestController::class, 'store'])->name('request.store');
Route::get('/request/today', [RequestController::class, 'showToday'])->name('request');