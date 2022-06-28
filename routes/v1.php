<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\SendController;
use Illuminate\Support\Facades\Route;

Route::get('/token', [AuthController::class, 'issueToken']);

Route::prefix('push')->name('push.')->middleware('client')->group(function () {
    Route::post('/sendDefault', [SendController::class, 'sendDefault'])->name('sendDefault');
});
