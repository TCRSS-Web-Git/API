<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\UserTitleController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::get('csrf-cookie', [CsrfCookieController::class, 'show'])->name('csrf');

Route::middleware('auth:sanctum')->group(function () {
    // Users and Profile
    Route::get('user', [UserProfileController::class, 'me'])->name('user.me');
    Route::put('user', [UserProfileController::class, 'updateProfile'])->name('user.update');
    Route::put('user/password', [UserProfileController::class, 'updatePassword'])->name('user.password');
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);

    Route::get('categories/{type}', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories/{type}', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{type}/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::put('categories/{type}/{category}', [CategoryController::class, 'update'])->name('categories.show');
    Route::patch('categories/{type}/{category}', [CategoryController::class, 'update']);
    Route::delete('categories/{type}/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::apiResource('blogs', BlogController::class);
});

Route::get('titles', [UserTitleController::class, 'index'])->name('users.titles.index');
