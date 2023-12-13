<?php

namespace App\Providers;

use App\Services\GeneratePivotTableService;
use Illuminate\Support\ServiceProvider;

class FacadeServiceProvider extends ServiceProvider
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
        $this->app->bind(
            GeneratePivotTableService::class
        );
    }
}
