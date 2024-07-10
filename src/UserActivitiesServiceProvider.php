<?php

namespace AndyAntunes\UserActivities;

use AndyAntunes\UserActivities\Console\LaravelActivityObserverGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class UserActivitiesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('andyatunes-user-activities')
            ->hasConfigFile('recent-activity');
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->offerPublishing();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommand();

        //Register Config file
        $this->mergeConfigFrom(
            __DIR__ . '/config/recent-activity.php',
            'config',
        );
    }

    protected function registerCommand()
    {
        //Register generate command
        $this->commands([
            LaravelActivityObserverGenerator::class,
        ]);
    }

    protected function offerPublishing(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        if (!function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        //Publish Config
        $this->publishes([
            __DIR__ . '/config/recent-activity.php' => config_path('recent-activity.php'),
        ], 'config');

        //Publish Migration
        $this->publishes([
            __DIR__ . '/database/migrations/create_recent_activities_table.php.stub' => $this->getMigrationFileName('create_recent_activities_table.php'),
        ], 'migrations');
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     */
    protected function getMigrationFileName(string $migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make([$this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR])
            ->flatMap(fn ($path) => $filesystem->glob($path . '*_' . $migrationFileName))
            ->push($this->app->databasePath() . "/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}
