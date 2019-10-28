<?php

namespace App\Services\Pools;

class PoolManager
{

    /**
     * Start pools
     */
    public static function startPools()
    {
        if (env('POOL_AUTO_START_REDIS')) {
            RedisPool::make();
        }

        if (env('POOL_AUTO_START_MYSQL')) {
            MySQLPool::make();
        }
    }

    /**
     * Destroy pools
     */
    public static function destroyPools()
    {
        RedisPool::destroy();
        MySQLPool::destroy();
    }
}