<?php

namespace AndyAntunes\HelperCommands\Console;

use AndyAntunes\HelperCommands\Services\FactoryGenerator;
use AndyAntunes\HelperCommands\Support\Facades\Model;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\select;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class FactoryGeneratorCommand extends Command
{
    protected $signature = 'helper:factory {model?} {quantity?} {withEvents?}';

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

        $recordsQuantity = $this->argument('quantity');
        if (!$recordsQuantity) {
            $recordsQuantity = text(
                label: 'How many records do you want to create?',
                validate: fn(string $value) => $this->recordsQuantityValidation($value),
            );
        }

        $withEvents = $this->argument('withEvents');
        if (!$withEvents) {
            $withEvents = confirm('Do you want to create without events?', true);
        }

        $generator = new FactoryGenerator(
            class: Str::ucfirst($model),
            modelName: Str::ucfirst($model),
            recordsQuantity: $recordsQuantity,
            withEvents: $withEvents
        );

        $generator->generate();

        $this->info('Code successfully merged into the seeder!');

        return 1;
    }

    private function recordsQuantityValidation(string $value): ?string
    {
        return  match (true) {
            !is_numeric($value) => 'The value must be numeric',
            (int) $value === 0 => 'The value must be a number greater than 0',
            default => null,
        };
    }
}
