<?php

namespace App\Providers;

use App\Models\AppSetting;
use App\Models\Currency;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        // Support older MySQL/MariaDB index key limits.
        Schema::defaultStringLength(191);

        if (Schema::hasTable('app_settings')) {
            $timezone = AppSetting::getValue('timezone', config('app.timezone'));
            if ($timezone) {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);
            }
        }

        if (Schema::hasTable('currencies')) {
            $defaultCurrency = Currency::query()->where('is_default', true)->first();
            View::share('appDefaultCurrency', $defaultCurrency);
        } else {
            View::share('appDefaultCurrency', null);
        }
    }
}
