<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\SendController;
use Illuminate\Support\Facades\Route;

Route::post('/token', [AuthController::class, 'issueToken']);

Route::prefix('push')->name('push.')->middleware('client')->group(function () {
    Route::post('/send-default', [SendController::class, 'sendDefault'])->name('sendDefault');
    Route::post('/send-each', [SendController::class, 'sendEach'])->name('sendEach');
});
