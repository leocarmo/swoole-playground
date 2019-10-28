<?php

namespace App\Services\Pools;

use App\Exceptions\DatabaseException;
use App\Services\Database\MySQLQuery;
use Swoole\Coroutine\MySQL;

class MySQLPool
{

    use Pool;

    /**
     * @return MySQLQuery|null
     */
    protected static function connect()
    {
        ($mysql = new MySQL())->connect([
            'host' => env('MYSQL_HOST', '0.0.0.0'),
            'port' => env('MYSQL_PORT', 3306),
            'user' => env('MYSQL_USER'),
            'password' => env('MYSQL_PASS'),
            'database' => env('MYSQL_DATABASE'),
            'charset' => env('MYSQL_CHARSET'),
            'timeout' => env('MYSQL_TIMEOUT'),
        ]);

        if ($mysql instanceof MySQL) {
            return new MySQLQuery($mysql);
        }

        return null;
    }

    /**
     * @param MySQLQuery $conn
     */
    protected static function disconnect($conn)
    {
        if (self::validate($conn)) {
            go(function () use ($conn) {
                $conn->conn()->close();
            });
        }
    }

    /**
     * @param MySQLQuery $conn
     *
     * @return bool
     */
    protected static function isConnected($conn)
    {
        try {
            return $conn->conn()->connected;
        } catch (\Throwable $e) {
            if (env('APP_DEBUG')) {
                dump($e);
            }
            return false;
        }
    }

    /**
     * @param $conn
     * @return bool
     */
    protected static function validate($conn)
    {
        return $conn instanceof MySQLQuery;
    }

    /**
     * @param $message
     * @param $code
     *
     * @throws DatabaseException
     */
    protected static function throwConnectionError($message, $code)
    {
        throw new DatabaseException($message, $code);
    }
}
