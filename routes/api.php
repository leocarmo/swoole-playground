<?php

/**
 * Define application routes
 *
 * @var \FastRoute\RouteCollector $router
 */

$router->addRoute(
    'GET',
    '/health-check',
    function (\App\Http\Request $request) {
        return response($request->all());
    }
);

$router->addRoute(
    'GET',
    '/handler/[{title}]',
    'HomeHandler@index'
);

$router->addRoute(
    'GET',
    '/mysql',
    'MySQLHandler@index'
);

$router->addRoute(
    'POST',
    '/mysql',
    'MySQLHandler@store'
);

$router->addRoute(
    'DELETE',
    '/mysql',
    'MySQLHandler@destroy'
);

$router->addRoute(
    'GET',
    '/redis',
    'RedisHandler@index'
);

$router->addRoute(
    'GET',
    '/redis2',
    'RedisHandler@show'
);

$router->addRoute(
    'GET',
    '/options/[{title}]',
    [
        'handler' => 'HomeHandler@index',
        'middleware' => [
            \App\Middleware\ExampleMiddleware::class,
        ],
    ]
);