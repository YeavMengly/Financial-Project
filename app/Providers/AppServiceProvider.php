<?php

namespace App\Providers;

use App\Models\BeginCredit\Agency;
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
        View::composer('layouts.sidebar', function ($view) {
            $agencies = Agency::orderBy('agencyNumber')->get();
            $view->with('agencies', $agencies);
        });
    }
}
