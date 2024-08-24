<?php

namespace AndyAntunes\HelperCommands\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AndyAntunes\HelperCommands\LogActivity
 * @method static RecentActivity setUserId(int $userId = null)
 * @method static RecentActivity setAction(string $action)
 * @method static RecentActivity setRecordId(int $recordId)
 * @method static RecentActivity create()
 */
class LogActivity extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AndyAntunes\HelperCommands\LogActivity::class;
    }
}
