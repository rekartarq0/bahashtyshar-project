<?php

use App\Http\Controllers\BackUpController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceNormalController;
use App\Http\Controllers\InvoiceProjectsController;
use App\Http\Controllers\InvoicesAsaishController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\MulksDashboardController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\ProjectStageController;
use App\Http\Controllers\RepportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TypeProjectController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/storage/{path}', function ($path) {
    $file = storage_path('app/public/'.$path);
    if (! file_exists($file)) {
        abort(404);
    }
    $type = mime_content_type($file);

    return Response::make(file_get_contents($file), 200, ['Content-Type' => $type]);
})->where('path', '.*');

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
       Route::get('/mulks-dashboard', [MulksDashboardController::class, 'index'])
         ->name('mulks.dashboard');
    Route::get('/screenOne', [DashboardController::class, 'screenOne'])->name('screenOne');
    Route::get('/screenTwo', [DashboardController::class, 'screenTwo'])->name('screenTwo');
    // users
    Route::resource('users', UsersController::class)->names('users');
    // roles
    Route::resource('roles', RoleController::class)->names('roles');
    // categories
    Route::resource('categories', CategoryController::class)->names('categories');
    // projects
    Route::resource('projects', ProjectsController::class)->names('projects');
    // typeProject
    Route::resource('typemulk', TypeProjectController::class)->names('typemulk');
    Route::resource('locations', LocationsController::class)->names('locations');

    // projectStages
    Route::resource('projectStages', ProjectStageController::class)->names('projectStages');
    Route::post('/projectStages/acceptDesign', [ProjectStageController::class, 'acceptDesign']);
    Route::post('/projectStages/back', [ProjectStageController::class, 'backToDesign'])->name('projectStages.back');
    Route::post('/projectStages/completePricing', [ProjectStageController::class, 'completePricing'])
        ->name('projectStages.completePricing');

    Route::delete('/customers/{customer}/mulks/{mulk}',
        [ProjectStageController::class, 'detachMulk'])
        ->name('customers.mulks.detach');
    // darxsta
    Route::resource('invoiceProjects', InvoiceProjectsController::class)
        ->names('invoiceProjects');

    Route::resource('invoiceAsaish', InvoicesAsaishController::class)->names('invoicesAsaish');
    Route::resource('invoicenormal', InvoiceNormalController::class)->names('invoicenormal');

    Route::post('/projectStages/back/prev', [ProjectStageController::class, 'backPrevStage'])->name('projectStages.backPrevStage');

    Route::post('/customers/{id}/update/dashboard', [CustomersController::class, 'update']);
    Route::post('/customers/store/dashboard', [CustomersController::class, 'store']);
    Route::get('/customers/{id}', [CustomersController::class, 'show']);
    Route::delete('/customers/{id}', [CustomersController::class, 'destroy']);
    Route::get('/customers', [CustomersController::class, 'index'])->name('customers.index');
    Route::post('/customers/archive', [CustomersController::class, 'archive']);
    Route::post('/projects/archivedwithoutmulk', [ProjectsController::class, 'archive']);

    Route::post('/projects/store/dashboard', [ProjectsController::class, 'store'])
        ->name('dashboard.store');
    Route::post('/projects/{id}/update/dashboard', [ProjectsController::class, 'update']);

    Route::post('/projectStages/quit', [ProjectStageController::class, 'quitStage']);
    // projects archived
    Route::post('/projects/archivedwithmulks', [ProjectsController::class, 'archivedwithmulks']);

    Route::get('/repport', [RepportController::class, 'index'])->name('repport');
    Route::get('/repport/projects', [RepportController::class, 'projects'])->name('repport.projects');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings/update', [settingsController::class, 'update'])->name('settings.update');

    // BackUp
    Route::post('/backup', [BackUpController::class, 'backup'])->name('backup');
});
