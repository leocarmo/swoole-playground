<?php

namespace App\Services\Pools;

use Swoole\Coroutine\Channel;
use Swoole\Timer;

trait Pool
{
    /**
     * @var Channel
     */
    protected static $pool;

    /**
     * @var bool
     */
    protected static $active = false;

    /**
     * @var integer
     */
    protected static $size;

    /**
     * @var int
     */
    protected static $timer_id;

    /**
     * @param int $size
     * @return bool
     */
    public static function make(int $size = null)
    {
        if (self::$pool) {
            return true;
        }

        if (env('APP_DEBUG')) {
            dump(sprintf('%s starting...', get_called_class()));
        }

        self::$size = $size ?: (int) env('POOL_SIZE', 100);
        self::$pool = new Channel(self::$size);

        go(function () {
            for ($i = 0; $i < self::$size; $i++) {
                if (self::validate($conn = self::connect())) {
                    self::put($conn);
                }
            }
        });

        self::$active = true;

        self::startTimerTick();

        return true;
    }

    /**
     * @return mixed
     */
    public static function get()
    {
        if (! self::$pool || self::$pool->isEmpty()) {
            self::make();
        }

        $conn = self::$pool->pop();

        if (! self::isConnected($conn)) {
            $conn = self::connect();
        }

        if (! self::validate($conn)) {
            self::throwConnectionError('Connection pool is not healthy', 500);
        }

        return $conn;
    }

    /**
     * @param $conn
     */
    public static function put($conn)
    {
        if (! self::$pool) {
            self::make();
        }

        if (! self::validate($conn)) {
            self::throwConnectionError('Invalid connection type', 500);
        }

        if (! self::isConnected($conn)) {
            return;
        }

        if (self::$pool->isFull() || ! self::$pool->push($conn)) {
            self::disconnect($conn);
            return;
        }
    }

    /**
     * @return int
     */
    public static function length()
    {
        if (! self::$pool) {
            self::make();
        }

        return self::$pool->length();
    }

    /**
     * @return Channel
     */
    public static function pool()
    {
        if (! self::$pool) {
            self::make();
        }

        return self::$pool;
    }

    /**
     * @return bool
     */
    public static function destroy()
    {
        if (! self::$pool) {
            return true;
        }

        for ($i = 0; $i <= self::length(); $i++) {
            self::disconnect(
                self::get()
            );
        }

        Timer::clear(self::$timer_id);

        self::$pool->close();
        self::$pool = null;
        self::$active = false;

        return true;
    }

    protected static function startTimerTick()
    {
        if (! env('POOL_TIMER_ACTIVE')) {
            return;
        }

        go(function () {
            self::$timer_id = Timer::tick(
                round(env('POOL_TIMER_INTERVAL', 10)) * 1000,
                function () {
                    if (env('APP_DEBUG')) {
                        dump(sprintf(
                            '[%s][%s] Pool recycle running...',
                            date('Y-m-d H:i:s'),
                            get_called_class()
                        ));
                    }

                    if (! self::$active || ! self::$pool) {
                        return;
                    }

                    $pool_size = self::length();
                    for ($i = 1; $i <= $pool_size; $i++) {
                        self::put(self::get());
                    }
                }
            );
        });
    }
}
