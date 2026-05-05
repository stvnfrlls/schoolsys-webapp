<?php

use App\Http\Controllers\User\ActivityLogController;
use App\Http\Controllers\User\PermissionController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Curriculum\GradeLevelController;
use App\Http\Controllers\Curriculum\SectionController;
use App\Http\Controllers\Curriculum\SubjectController;
use App\Http\Controllers\Curriculum\SubjectPerLevelController;
use App\Http\Controllers\User\StudentController;
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
    Route::resource('sections', SectionController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('subjectperlevel', SubjectPerLevelController::class);

    Route::resource('students', StudentController::class);
});

require __DIR__ . '/auth.php';
