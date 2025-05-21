<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\RequestController;

Route::post('/request', [RequestController::class, 'store']);
Route::get('/messages', [RequestController::class, 'loadMessages']);
Route::post('/messages', [RequestController::class, 'sendMessage']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
