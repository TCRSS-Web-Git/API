<?php

use App\Http\Controllers\AnnualReportController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\AwardImageController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductAndServiceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TemporaryMediaController;
use App\Http\Controllers\ThailandGeographyController;
use App\Http\Controllers\User\InviteController;
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

    Route::post('temporary-media', [TemporaryMediaController::class, 'store'])->name('temporary_media.store');

    Route::get('categories/{type}', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories/{type}', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{type}/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::put('categories/{type}/{category}', [CategoryController::class, 'update'])->name('categories.show');
    Route::patch('categories/{type}/{category}', [CategoryController::class, 'update']);
    Route::delete('categories/{type}/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('tags/{type}', [TagController::class, 'index'])->name('tags.index');

    Route::patch('awards/reorder', [AwardController::class, 'reorder'])->name('awards.reorder');
    Route::apiResource('awards', AwardController::class);

    Route::apiResource('blogs', BlogController::class);
    Route::apiResource('careers', CareerController::class);

    Route::patch('award-images/reorder', [AwardImageController::class, 'reorder'])->name('award-images.reorder');
    Route::apiResource('award-images', AwardImageController::class);

    Route::patch('products-and-services/reorder', [ProductAndServiceController::class, 'reorder'])->name('products-and-services.reorder');
    Route::apiResource('products-and-services', ProductAndServiceController::class);

    Route::patch('annual-reports/reorder', [AnnualReportController::class, 'reorder'])->name('annual-reports.reorder');
    Route::apiResource('annual-reports', AnnualReportController::class);

    Route::post('/invite/resend/{user}', [InviteController::class, 'resend'])->name('invite.resend');
});
Route::get('geography/provinces', [ThailandGeographyController::class, 'provinces'])->name('geography.provinces');
Route::get('geography/provinces/{province}/districts', [ThailandGeographyController::class, 'districts'])->name('geography.districts');
Route::get('geography/districts/{district}/subdistricts', [ThailandGeographyController::class, 'subdistricts'])->name('geography.subdistricts');

Route::get('titles', [UserTitleController::class, 'index'])->name('users.titles.index');
Route::put('/accept-user-invitation', [InviteController::class, 'accept'])->middleware('signed.invite:relative')->name('accept.users.invitation');

Route::name('public.')->prefix('public')->group(function () {
    Route::name('blogs.')->prefix('blogs')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('/{blog}', [BlogController::class, 'show'])->name('show');
    });
});
