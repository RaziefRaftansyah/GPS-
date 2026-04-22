<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        if ($this->app->runningInConsole()) {
            return;
        }

        if (! empty(config('app.asset_url'))) {
            return;
        }

        $request = request();
        $basePath = rtrim((string) $request->getBasePath(), '/');

        if ($basePath !== '') {
            URL::forceRootUrl($request->getSchemeAndHttpHost().$basePath);
        }
    }
}
