<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('csrf-cookie', [CsrfCookieController::class, 'show'])->name('csrf');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'me'])->name('users.me');
    Route::apiResource('users', UserController::class);
});
