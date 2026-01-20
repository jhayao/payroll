<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::controller(ApiController::class)->group(function () {

    Route::prefix('dtr/')->group(function () {
        Route::post('make-log', 'makeLog');
        Route::post('view', 'viewDTR');
    });

    Route::post('timekeeper-login', 'verifyTimekeeper');
    Route::get('employees', 'getEmployees');
    
    // Timekeeper Routes
    Route::get('timekeeper/projects', 'getTimekeeperProjects');
    Route::get('timekeeper/employees', 'getTimekeeperEmployees');
    Route::get('timekeeper/attendance', 'getTimekeeperAttendance');

});
