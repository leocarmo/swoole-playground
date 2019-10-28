<?php

namespace App\Services\Pools;

use App\Exceptions\RedisException;
use Swoole\Coroutine\Redis;

class RedisPool
{

    use Pool;

    /**
     * @return Redis
     */
    protected static function connect()
    {
        ($redis = new Redis())->connect(
            env('REDIS_HOST', 'localhost'),
            env('REDIS_PORT', 6379),
            env('REDIS_TIMEOUT', 5.0)
        );

        return $redis;
    }

    /**
     * @param Redis $conn
     *
     * @return void
     */
    protected static function disconnect($conn)
    {
        if (self::validate($conn)) {
            go(function () use ($conn) {
                $conn->close();
            });
        }
    }

    /**
     * @param Redis $conn
     *
     * @return bool
     */
    protected static function isConnected($conn)
    {
        try {
            return $conn->connected;
        } catch (\Throwable $e) {
            if (env('APP_DEBUG')) {
                dump($e);
            }
            return false;
        }
    }

    /**
     * @param mixed $conn
     *
     * @return bool
     */
    protected static function validate($conn)
    {
        return $conn instanceof Redis;
    }

    /**
     * @param $message
     * @param $code
     *
     * @throws RedisException
     */
    protected static function throwConnectionError($message, $code)
    {
        throw new RedisException($message, $code);
    }
}
