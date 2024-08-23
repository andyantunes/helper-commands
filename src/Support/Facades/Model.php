<?php

namespace AndyAntunes\HelperCommands\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AndyAntunes\HelperCommands\Support\Models\Model
 * @method static Model all() Returns an array of all models
 */
class Model extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AndyAntunes\HelperCommands\Support\Models\Model::class;
    }
}
