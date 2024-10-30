<?php

namespace App\Providers;

use App\Models\AnnualReport;
use App\Models\Award;
use App\Models\AwardImage;
use App\Models\Blog;
use App\Models\Career;
use App\Models\Category;
use App\Models\District;
use App\Models\ProductAndService;
use App\Models\Province;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Scribe;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());

        Password::defaults(function () {
            return Password::min(8);
        });

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            $referrer = request()->headers->get('referer');
            // if referrer url is ADMIN_URL use admin url instead of frontend
            if (str_contains($referrer, config('app.admin_url'))) {
                return config('app.admin_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
            }

            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // Allow Scribe to generate docs with authenticated user
        if (class_exists(Scribe::class)) {
            Scribe::beforeResponseCall(function (Request $request, ExtractedEndpointData $endpointData) {
                $user = User::first();
                Sanctum::actingAs($user, ['*']);
            });
        }

        // Please keep it in alphabetical order.
        Route::bind('annual_report', function ($value) {
            return AnnualReport::findByHashOrFail($value);
        });

        Route::bind('award', function ($value) {
            return Award::findByHashOrFail($value);
        });

        Route::bind('award_image', function ($value) {
            return AwardImage::findByHashOrFail($value);
        });

        Route::bind('blog', function ($value) {
            return Blog::where('slug', $value)->first() ?? Blog::findByHashOrFail($value);
        });

        Route::bind('category', function ($value) {
            return Category::findByHashOrFail($value);
        });

        Route::bind('career', function ($value) {
            return Career::findByHashOrFail($value);
        });

        Route::bind('district', function ($value) {
            return District::where('id', District::decodeHash($value))->firstOrFail();
        });

        Route::bind('products_and_service', function ($value) {
            return ProductAndService::findByHashOrFail($value);
        });

        Route::bind('province', function ($value) {
            return Province::where('id', Province::decodeHash($value))->firstOrFail();
        });

        Route::bind('user', function ($value) {
            return User::findByHashOrFail($value);
        });

        Route::bind('role', function ($value) {
            return Role::findByHashOrFail($value);
        });
    }
}
