<?php

namespace App\Middleware;

use App\Http\Request;

class ExampleMiddleware
{

    public function handle(Request $request)
    {
        if ($request->param('abort')) {
            throw new \Exception('Aborted!', 412);
        }
    }
}
