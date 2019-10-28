<?php

namespace App\Handlers;

use App\Services\Pools\RedisPool;

class RedisHandler extends Handler
{

    public function index()
    {
        RedisPool::make();

        go(function () {
            for ($c = 0; $c < 1000; $c++) {
                go(function () use ($c) {
                    for ($n = 0; $n < 100; $n++) {
                        $redis = RedisPool::get();
                        dump($redis);

                        $redis->set("swoole:awesome-{$c}-{$n}", 'swoole', 30);
//                        assert($redis->set("swoole:awesome-{$c}-{$n}", 'swoole', 30));
//                        assert($redis->get("swoole:awesome-{$c}-{$n}") === 'swoole');
                        //assert($redis->delete("swoole:awesome-{$c}-{$n}"));

                        RedisPool::put($redis);
                    }
                });
            }
        });

        return response([
            'len' => RedisPool::length(),
        ]);
    }

    public function show()
    {
        /** @var \Redis $redis */
        $redis = RedisPool::get();

        assert($redis->set("swoole:awesome", 'swoole'));
        assert($redis->get("swoole:awesome") === 'swoole');

        RedisPool::put($redis);

        return response();
    }
}
