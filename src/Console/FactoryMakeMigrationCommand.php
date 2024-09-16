<?php

namespace AndyAntunes\HelperCommands\Console;

use Illuminate\Database\Console\Migrations\MigrateCommand;

class FactoryMigrateCommand extends MigrateCommand
{
    protected $name = 'migrate';

    /**
     * Execute the console command.
     *
     * @param bool|null
     */
    public function handle()
    {
        $result = parent::handle();

        $shouldRememberMe = config('helper-commands.factory_generation.remember_me');
        if ($shouldRememberMe && $result !== false) {
            $create = $this->confirm('Do you want to create a Factory?', true);
            if ($create) {
                $this->call('helper:factory');
            }
        }
    }
}
