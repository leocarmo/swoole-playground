<?php

namespace App\Kernel;

use App\Exceptions\HandlerException;
use App\Http\Response;
use App\Support\RouterResponses;
use function FastRoute\cachedDispatcher;
use Swoole\Http\Request;

class Router
{

    use RouterResponses;

    /**
     * @var \FastRoute\Dispatcher
     */
    protected $dispatcher;

    /**
     * Router constructor.
     *
     * @param callable $routes
     */
    public function __construct(callable $routes)
    {
        $this->dispatcher = cachedDispatcher(
            $routes,
            $this->dispatcherOptions()
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handleRequest(Request $request)
    {
        $request_method = $request->server['request_method'];
        $request_uri = $request->server['request_uri'];

        list($code, $options, $params) = $this->dispatcher->dispatch($request_method, $request_uri);

        if ($code === \FastRoute\Dispatcher::FOUND) {
            return $this->dispatchRequest($request, $options, $params);
        }

        if ($code === \FastRoute\Dispatcher::NOT_FOUND) {
            return $this->routeNotFound();
        }

        if ($code === \FastRoute\Dispatcher::METHOD_NOT_ALLOWED) {
            return $this->methodNotAllowed();
        }

        return $this->internalServerError();
    }

    /**
     * @return array
     */
    protected function dispatcherOptions()
    {
        return [
            'cacheFile' => Application::storagePath('cache/routes.cache'),
            'cacheDisabled' => ! (bool) env('CACHE_ROUTES', true),
        ];
    }

    protected function dispatchRequest(Request $raw_request, $options, $params)
    {
        $request = $this->makeRequest($raw_request, $params);

        try {
            $response = $this->executeHandler($request, $options);

            if ($response instanceof Response) {
                return $response;
            }

            return $this->invalidResponseFormat();
        } catch (\Throwable $throwable) {
            return $this->handleException($throwable);
        }
    }

    /**
     * @param Request $raw_request
     * @param $params
     *
     * @return \App\Http\Request
     */
    protected function makeRequest(Request $raw_request, $params)
    {
        return new \App\Http\Request($raw_request, $params);
    }

    /**
     * @param $request
     * @param $options
     *
     * @return Response
     */
    protected function executeHandler($request, $options)
    {
        if (is_callable($options)) {
            return call_user_func($options, $request);
        }

        if (is_string($options)) {
            if (! $handler = $this->parseHandler($options)) {
                return $this->invalidHandler();
            }

            return $this->executeHandlerClass($handler, $request);
        }

        if (is_array($options) && isset($options['handler'])) {
            if (! $handler = $this->parseHandler($options['handler'])) {
                return $this->invalidHandler();
            }

            $this->executeMiddleware(
                $options['middleware'] ?? [],
                $request
            );

            return $this->executeHandlerClass($handler, $request);
        }

        return $this->invalidHandler();
    }

    /**
     * @param $options
     *
     * @return array|bool
     */
    protected function parseHandler($options)
    {
        list($class, $method) = explode('@', $options);

        $class = '\\App\\Handlers\\' . $class;

        if (! method_exists($class, $method)) {
            return false;
        }

        return [
            'class' => $class,
            'method' => $method
        ];
    }

    /**
     * @param $handler
     * @param $request
     *
     * @return Response
     */
    protected function executeHandlerClass($handler, $request)
    {
        return call_user_func_array(
            [new $handler['class']($request), $handler['method']],
            []
        );
    }

    /**
     * @param $middlewares
     * @param $request
     */
    protected function executeMiddleware($middlewares, $request)
    {
        foreach ($middlewares as $middleware) {
            call_user_func_array([$middleware, 'handle'], [$request]);
        }
    }

    /**
     * @param \Throwable $t
     *
     * @return Response
     */
    protected function handleException(\Throwable $t)
    {
        $handler_exception = new HandlerException(
            $t->getMessage(), $t->getCode(), $t
        );

        $handler_exception->report();

        return $handler_exception->render();
    }
}
