<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaylistController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/playlists', [PlaylistController::class, 'index']);
Route::post('/playlists', [PlaylistController::class, 'store']);
Route::put('/playlists/{id}', [PlaylistController::class, 'update']);
Route::delete('/playlists/{id}', [PlaylistController::class, 'destroy']);
Route::post('/playlists/{id}/apply', [PlaylistController::class, 'applyPlaylist']);
