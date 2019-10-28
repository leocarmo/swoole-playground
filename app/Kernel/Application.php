<?php

namespace App\Kernel;

use App\Support\ApplicationPatch;

class Application
{

    use ApplicationPatch;

    /**
     * @var HttpServer
     */
    protected $http_server;

    /**
     * @var Router
     */
    protected $router;

    /**
     * Application constructor.
     *
     * @param $base_patch
     */
    public function __construct($base_patch)
    {
        self::$base_patch = $base_patch;
    }

    /**
     * Setup application
     */
    public function setup()
    {
        $this->http_server = new HttpServer();
    }

    /**
     * Construct application routes
     *
     * @param callable $routes
     */
    public function routes(callable $routes)
    {
        $this->router = new Router($routes);

        $this->http_server->pushRouter(
            $this->router
        );
    }

    /**
     * Start application for handle requests
     */
    public function start()
    {
        $this->http_server->start();
    }
}
