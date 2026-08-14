<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        if ($this->app->isProduction() || (request()->header('x-forwarded-proto') === 'https') || str_contains(request()->header('host', ''), 'vercel.app')) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            static $appLogo = null;
            static $appName = null;

            if ($appLogo === null) {
                try {
                    if (Schema::hasTable('settings')) {
                        $logoVal = Setting::where('key', 'app_logo')->value('value');
                        if ($logoVal) {
                            $appLogo = (str_starts_with($logoVal, 'data:') || str_starts_with($logoVal, 'http://') || str_starts_with($logoVal, 'https://'))
                                ? $logoVal
                                : asset($logoVal);
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore
                }
                $appLogo = $appLogo ?: asset('images/logo-tasikmalaya.png');
            }

            if ($appName === null) {
                try {
                    if (Schema::hasTable('settings')) {
                        $appName = Setting::where('key', 'app_name')->value('value');
                    }
                } catch (\Throwable $e) {
                    // Ignore
                }
                $appName = $appName ?: 'SIAP Neglasari';
            }

            $view->with('appLogo', $appLogo)->with('appName', $appName);
        });
    }
}

