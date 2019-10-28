<?php

namespace App\Handlers;

class HomeHandler extends Handler
{

    public function index()
    {
        $result = [
            'message' => 'Hello world!',
            'vars' => [
                'all' => $this->request->all(),
                'params' => $this->request->params(),
                'hello' => $this->request->param('hello', 'default'),
                'headers' => $this->request->headers(),
                'x-hello-world' => $this->request->header('x-hello-world'),
            ],
        ];

        return response($result, $this->request->param('status'), [
            'x-hello-world' => $this->request->header('x-hello-world', 'no-value'),
        ]);
    }
}