<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ProjectStageController;
use App\Http\Controllers\RepportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
// fastfood applicartions
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard') // Redirect authenticated users to the dashboard
        : redirect()->route('login');    // Redirect unauthenticated users to the login page
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    //users 
    Route::resource('users', UsersController::class)->names('users');
    //roles
    Route::resource('roles', RoleController::class)->names('roles');
    //categories
    Route::resource('categories', CategoryController::class)->names('categories');
    //projects
    Route::resource('projects', ProjectsController::class)->names('projects');
    //projectStages
    Route::resource('projectStages', ProjectStageController::class)->names('projectStages');

    Route::get('/repport', [RepportController::class, 'index'])->name('repport');
    Route::get('/repport/projects', [RepportController::class, 'projects'])->name('repport.projects');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/update', [settingsController::class, 'update'])->name('settings.update');
});
