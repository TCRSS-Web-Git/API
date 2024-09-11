<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [UserController::class, 'me'])->name('users.me');
    Route::apiResource('users', UserController::class);
});
