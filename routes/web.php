<?php

use App\Http\Controllers\User\ActivityLogController;
use App\Http\Controllers\User\PermissionController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Curriculum\GradeLevelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))
        ->middleware('verified')
        ->name('dashboard');

    Route::resource('users', UserController::class);
    Route::put('users/{user}/password', [UserController::class, 'updatePassword'])
        ->name('users.update-password');
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activitylogs.index');
    Route::resource('gradelevels', GradeLevelController::class)->parameters(['gradelevels' => 'gradeLevel']);
});

require __DIR__ . '/auth.php';
