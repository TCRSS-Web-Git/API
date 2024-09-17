<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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

        Route::bind('user', function ($value) {
            return User::findByHashOrFail($value);
        });

        Route::bind('role', function ($value) {
            return Role::findByHashOrFail($value);
        });
    }
}
