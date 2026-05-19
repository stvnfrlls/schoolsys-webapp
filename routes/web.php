<?php

use App\Http\Controllers\Curriculum\AttendanceController;
use App\Http\Controllers\Curriculum\EnrollmentController;
use App\Http\Controllers\User\ActivityLogController;
use App\Http\Controllers\User\PermissionController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Curriculum\GradeLevelController;
use App\Http\Controllers\Curriculum\ScheduleController;
use App\Http\Controllers\Curriculum\SchoolYearController;
use App\Http\Controllers\Curriculum\SectionController;
use App\Http\Controllers\Curriculum\SubjectController;
use App\Http\Controllers\Curriculum\SubjectPerLevelController;
use App\Http\Controllers\User\FacultyController;
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
    Route::resource('enrollments', EnrollmentController::class);
    Route::resource('schoolyears', SchoolYearController::class);
    Route::resource('faculty', FacultyController::class);

    Route::get('schedules/timetable', [ScheduleController::class, 'timetable'])
        ->name('schedules.timetable');
    Route::resource('schedules', ScheduleController::class);

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('load-schedules', [AttendanceController::class, 'loadSchedules'])->name('load-schedules');
        Route::get('take',           [AttendanceController::class, 'take'])->name('take');
        Route::get('summary',        [AttendanceController::class, 'summary'])->name('summary');
    });

    Route::resource('attendance', AttendanceController::class)->except(['create', 'show']);
});

require __DIR__ . '/auth.php';
