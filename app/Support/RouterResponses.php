<?php

namespace App\Support;

trait RouterResponses
{

    protected function routeNotFound()
    {
        return response(['message' => 'Route not found'], 404);
    }

    protected function methodNotAllowed()
    {
        return response(['message' => 'Method Not Allowed'], 405);
    }

    protected function internalServerError()
    {
        return response(['message' => 'Something went wrong here!'], 500);
    }

    protected function invalidHandler()
    {
        return response(['message' => 'Invalid handler'], 500);
    }

    protected function invalidResponseFormat()
    {
        return response(['message' => 'Invalid response format'], 500);
    }
}