<?php

namespace App\Http;

class Response
{

    /**
     * @var array
     */
    protected $content;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var array
     */
    protected $headers;

    /**
     * Response constructor
     *
     * @param array $content
     * @param int $status
     * @param array $headers
     */
    public function __construct(array $content = [], $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return (int) $this->status;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return false|null|string
     */
    public function end()
    {
        return $this->content ? json_encode($this->content) : null;
    }
}
