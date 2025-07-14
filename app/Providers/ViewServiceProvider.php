<?php

namespace App\Providers;

use App\Models\BeginCredit\Agency;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        // Share agency list with the sidebar view
        View::composer('layouts.sidebar', function ($view) {
            $agencies = Agency::orderBy('agencyNumber')->get();
            $view->with('agencies', $agencies);
        });
    }
}
