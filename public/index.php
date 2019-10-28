<?php

require_once __DIR__ . '/../vendor/autoload.php';

(\Dotenv\Dotenv::create(
    dirname(__DIR__)
))->load();

$app = new \App\Kernel\Application(
    dirname(__DIR__)
);

$app->setup();

$app->routes(function(\FastRoute\RouteCollector $router) {
    require dirname(__DIR__) . '/routes/api.php';
});

$app->start();