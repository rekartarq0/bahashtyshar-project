<?php

use App\Http\Controllers\ApiRepportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('report/projects',[ApiRepportController::class, 'projects']);