<?php

namespace App\Handlers;

use App\Http\Request;

class Handler
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * Handler constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}