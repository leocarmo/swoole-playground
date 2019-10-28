<?php

namespace App\Services\Database;

use App\Exceptions\DatabaseException;
use Swoole\Coroutine\MySQL;
use Swoole\Coroutine\Mysql\Statement;

class MySQLQuery
{

    /**
     * @var MySQL
     */
    protected $conn;

    /**
     * MySQLQuery constructor
     *
     * @param MySQL $conn
     */
    public function __construct(MySQL $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param string $query
     * @param array $binds
     * @return array|bool
     *
     * @throws DatabaseException
     */
    public function queryBind(string $query, $binds = [])
    {
        /** @var Statement $stmt */
        if (! $stmt = $this->conn->prepare($query)) {
            $this->handleError();
        }

        if (($result = $stmt->execute($binds, env('MYSQL_TIMEOUT'))) === false) {
            $this->handleError();
        }

        return $result;
    }

    /**
     * @return MySQL
     */
    public function conn()
    {
        return $this->conn;
    }

    /**
     * @throws DatabaseException
     */
    protected function handleError()
    {
        if (env('APP_DEBUG')) {
            dump($this->conn->error);
        }

        if (env('MYSQL_THROW_ERRORS')) {
            throw new DatabaseException($this->conn->error, 500);
        }
    }
}
