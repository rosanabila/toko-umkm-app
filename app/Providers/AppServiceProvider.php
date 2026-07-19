<?php

namespace App\Providers;

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
        view()->composer('seller.*', function ($view) {
            if (auth()->check() && auth()->user()->isPenjual()) {
                $view->with('store', auth()->user()->store);
            }
        });
    }
}
