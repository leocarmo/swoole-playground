<?php

namespace App\Kernel;

use App\Services\Pools\PoolManager;
use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

class HttpServer
{

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var Router
     */
    protected $router;

    /**
     * HttpServer constructor.
     */
    public function __construct()
    {
        $this->server = new Server(
            getenv('APP_HOST'),
            getenv('APP_PORT')
        );
    }

    public function start()
    {
        $this->onStart();
        $this->onRequest();
        $this->bindWorkerEvents();
        $this->server->start();
    }

    /**
     * @param Router $router
     */
    public function pushRouter(Router $router)
    {
        $this->router = $router;
    }

    protected function onStart()
    {
        $this->server->on('start', function (Server $server) {
            echo sprintf(
                'Swoole http server is started at http://%s:%s' . PHP_EOL,
                getenv('APP_HOST'),
                getenv('APP_PORT')
            );
        });
    }

    protected function bindWorkerEvents()
    {
        $this->server->on('WorkerStart', function (Server $server) {
            PoolManager::startPools();
        });
        $this->server->on('WorkerStop', function (Server $server) {
            PoolManager::destroyPools();
        });
        $this->server->on('WorkerError', function (Server $server) {
            PoolManager::destroyPools();
        });
    }

    protected function onRequest()
    {
        $this->server->on('request', function (Request $request_raw, Response $response_server) {
            $response = $this->router->handleRequest($request_raw);

            foreach ($response->getHeaders() as $key => $value) {
                $response_server->header($key, $value);
            }

            $response_server->header('Content-Type', 'application/json');

            $response_server->status($response->getStatus());

            $response_server->end($response->end());
        });
    }
}
