<?php

namespace App\Handlers;

use App\Services\Database\MySQLQuery;
use App\Services\Pools\MySQLPool;

class MySQLHandler extends Handler
{

    /**
     * @return \App\Http\Response
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function index()
    {
        /** @var MySQLQuery $conn */
        $conn = MySQLPool::get();

        $result = $conn->queryBind('SELECT * FROM users WHERE id = ?', [
            $this->request->param('id'),
        ]);

        MySQLPool::put($conn);

        return response([
            'result' => $result,
            'len' => MySQLPool::length()
        ]);
    }

    /**
     * @return \App\Http\Response
     *
     * @throws \App\Exceptions\DatabaseException
     */
    public function store()
    {
        /** @var MySQLQuery $conn */
        $conn = MySQLPool::get();

        $result = $conn->queryBind('INSERT INTO users (name) VALUES (?)', [
            $this->request->param('name'),
        ]);

        MySQLPool::put($conn);

        return response([
            'result' => $result,
            'len' => MySQLPool::length()
        ], 201);
    }

    public function destroy()
    {
        MySQLPool::destroy();

        return response([
            'message' => 'Deleted',
        ], 200);
    }
}
