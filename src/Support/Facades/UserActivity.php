<?php

namespace AndyAntunes\UserActivities\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AndyAntunes\UserActivities\UserActivities
 * @method static RecentActivity setUserId(int $userId = null)
 * @method static RecentActivity setAction(string $action)
 * @method static RecentActivity setRecordId(int $recordId)
 * @method static RecentActivity create()
 */
class UserActivity extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AndyAntunes\UserActivities\UserActivities::class;
    }
}
