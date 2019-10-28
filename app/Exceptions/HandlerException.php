<?php

namespace App\Exceptions;

class HandlerException extends \Exception
{

    /**
     * Log exception
     */
    public function report()
    {
        if (env('APP_DEBUG', false)) {
            dump($this->getPrevious());
        }
    }

    /**
     * @return \App\Http\Response
     */
    public function render()
    {
        return response(
            ['messages' => $this->getMessage()],
            $this->getCode()
        );
    }
}