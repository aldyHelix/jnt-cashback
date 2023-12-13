<?php

namespace Modules\Processwizard\Providers;

use Illuminate\Support\ServiceProvider;

class ProcesswizardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/module.php',
            'processwizard'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMigration();

        $this->registerBladeView();

        $this->registerTranslations();

        $this->registerViewComponent();

        $this->registerCommand();
    }

    /**
     * Register list of command
     *
     * @return void
     */
    protected function registerCommand()
    {

        if ($this->app->runningInConsole()) {
            $this->commands([
                // InstallCommand::class,
            ]);
        }
    }

    /**
     * Load view component
     *
     * @return void
     */
    protected function registerViewComponent()
    {
        $this->loadViewComponentsAs('processwizard', [
            // Alert::class,
        ]);
    }

    /**
     * Register migration directory
     *
     * @return void
     */
    protected function registerMigration()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Databases/Migrations');
    }

    /**
     * Register blade view directory
     *
     * @return void
     */
    protected function registerBladeView()
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'processwizard');
    }

    /**
     * Register Translations directory
     *
     * @return void
     */
    protected function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'processwizard');
    }
}
