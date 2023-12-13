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
        // $this->app->singleton(\App\Services\CreateSchemaService::class);
        // $this->app->singleton(\App\Services\FileProcessingService::class);
        // $this->app->singleton(\App\Services\FlagUpdaterService::class);
        // $this->app->singleton(\App\Services\GenerateDPFService::class);
        // $this->app->singleton(\App\Services\GenerateExportService::class);
        // $this->app->singleton(\App\Services\GeneratePivotRekapService::class);
        // $this->app->singleton(\App\Services\GeneratePivotTableService::class);
        // $this->app->singleton(\App\Services\GeneratePivotZonasiService::class);
        // $this->app->singleton(\App\Services\GenerateSchemaService::class);
        // $this->app->singleton(\App\Services\GenerateSummaryService::class);
        // $this->app->singleton(\App\Services\GradingService::class);
        // $this->app->singleton(\App\Services\PivotService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
