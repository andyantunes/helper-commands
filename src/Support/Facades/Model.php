<?php

namespace AndyAntunes\UserActivities\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AndyAntunes\UserActivities\Support\Models\Model
 * @method static Model all() Returns an array of all models
 */
class Model extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AndyAntunes\UserActivities\Support\Models\Model::class;
    }
}
