<?php

namespace AndyAntunes\HelperCommands\Console;

use Illuminate\Foundation\Console\ModelMakeCommand;

class ActivityMakeModel extends ModelMakeCommand
{
    protected $name = 'make:model';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $result = parent::handle();

        $shouldAsk = config('helper-commands.log_activities.remember_me');
        if ($shouldAsk && $result !== false) {
            $confirm = $this->confirm("Do you want to create an activity?", true);
            if ($confirm) {
                $this->call('helper:activity');
            }
        }

        return $result;
    }
}
