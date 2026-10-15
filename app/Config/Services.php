<?php

namespace Config;

use App\Libraries\UrlFormatter;
use App\Services\CronTasks\CheckRobotsTxt;
use App\Services\CronTasks\ClearCache;
use App\Services\CronTasks\RemoveOldSessionFiles;
use App\Services\HubAccessPolicy;
use App\Services\HubOwnerResolver;
use App\Services\HubPageCache;
use App\Services\PseudoCron;
use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /**
     * Returns the shared formatter used for compact URL labels.
     */
    public static function urlFormatter(bool $getShared = true): UrlFormatter
    {
        if ($getShared) {
            return static::getSharedInstance('urlFormatter');
        }

        return new UrlFormatter();
    }

    public static function hubOwnerResolver(bool $getShared = true): HubOwnerResolver
    {
        if ($getShared) {
            return static::getSharedInstance('hubOwnerResolver');
        }

        return new HubOwnerResolver();
    }

    public static function hubAccessPolicy(bool $getShared = true): HubAccessPolicy
    {
        if ($getShared) {
            return static::getSharedInstance('hubAccessPolicy');
        }

        return new HubAccessPolicy();
    }

    public static function hubPageCache(bool $getShared = true): HubPageCache
    {
        if ($getShared) {
            return static::getSharedInstance('hubPageCache');
        }

        return new HubPageCache();
    }

    /**
     * Returns the shared pseudo-cron task coordinator.
     */
    public static function pseudoCron(bool $getShared = true): PseudoCron
    {
        if ($getShared) {
            return static::getSharedInstance('pseudoCron');
        }

        return new PseudoCron(
            new ClearCache(),
            new RemoveOldSessionFiles(),
            new CheckRobotsTxt(),
        );
    }
}
