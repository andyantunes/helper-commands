<?php

namespace AndyAntunes\HelperCommands\Services;

use Illuminate\Support\Str;

class ActivityGenerator
{
    use Traits\HandleStub;
    use Traits\GenerateObserver;

    public function __construct(
        public string $type,
        public string $class,
        public string $modelName,
        public string $modelVariable
    ) {}

    /**
     * @return void
     */
    public function generate(): void
    {
        match ($this->type) {
            'all' => $this->all()
        };
    }
}
