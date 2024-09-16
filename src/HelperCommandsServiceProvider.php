<?php

namespace AndyAntunes\HelperCommands;

use AndyAntunes\HelperCommands\Console\ActivityMakeModel;
use AndyAntunes\HelperCommands\Console\ActivityObserverGenerator;
use AndyAntunes\HelperCommands\Console\FactoryGeneratorCommand;
use AndyAntunes\HelperCommands\Console\FactoryMigrateCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\{Collection, ServiceProvider};

class HelperCommandsServiceProvider extends ServiceProvider
{
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

        $this->mergeConfigFrom(
            __DIR__ . '/config/helper-commands.php',
            'config',
        );

        $this->app->singleton('command.make.model', function ($app) {
            return $app[ActivityMakeModel::class];
        });

        $this->app->singleton(FactoryMigrateCommand::class, function ($app) {
            // The migrator is responsible for coordinating the process of running migrations.
            // It checks the status of applied migrations, registers new migrations in the database,
            // and handles executing or rolling back migrations as needed.
            // By injecting the migrator into the command, we ensure that the migration process
            // is properly controlled and that all necessary dependencies are available
            // during the command execution.

            $creator = $app['migrator'];
            $composer = $app['events'];

            return new FactoryMigrateCommand($creator, $composer);
        });
    }

    protected function registerCommand()
    {
        $this->commands([
            ActivityObserverGenerator::class,
            ActivityMakeModel::class,
            FactoryGeneratorCommand::class,
            FactoryMigrateCommand::class,
        ]);
    }

    protected function offerPublishing(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        if (!function_exists('config_path')) {
            return;
        }

        $this->publishes([
            __DIR__ . '/config/helper-commands.php' => config_path('helper-commands.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/database/migrations/create_log_activities_table.php.stub' => $this->getMigrationFileName('create_log_activities_table.php'),
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
            ->flatMap(fn($path) => $filesystem->glob($path . '*_' . $migrationFileName))
            ->push($this->app->databasePath() . "/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }
}
