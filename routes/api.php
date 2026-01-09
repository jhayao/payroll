<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::controller(ApiController::class)->group(function() {

    Route::prefix('dtr/')->group(function() {
        Route::post('make-log', 'makeLog');
        Route::post('view', 'viewDTR');
    });

    Route::post('timekeeper-login', 'verifyTimekeeper');

});