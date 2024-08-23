<?php

namespace AndyAntunes\UserActivities\Console;

use AndyAntunes\UserActivities\Services\FactoryGenerator;
use AndyAntunes\UserActivities\Support\Facades\Model;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\select;

class LaravelFactoryGenerator extends Command
{
    protected $signature = 'gen:factory {model?}';

    protected $description = 'Create a new Activity Observer class in any path';

    public function handle(): int
    {
        $model = $this->argument('model');

        if (!$model) {
            $model = select(
                label: 'What is the name of the model?',
                options: Model::all(),
                required: true
            );
        }

        $generator = new FactoryGenerator(
            class: Str::ucfirst($model),
            modelName: Str::ucfirst($model),
        );

        $generator->generate();

        return 1;
    }
}
