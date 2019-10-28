<?php

namespace App\Http;

class Request
{
    /**
     * @var \Swoole\Http\Request
     */
    protected $raw_request;

    /**
     * @var array
     */
    protected $raw_params;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var array
     */
    protected $servers;

    /**
     * @var array
     */
    protected $cookies;

    /**
     * Request constructor
     *
     * @param \Swoole\Http\Request $raw_request
     * @param $raw_params
     */
    public function __construct(\Swoole\Http\Request $raw_request, $raw_params = [])
    {
        $this->raw_request = $raw_request;
        $this->raw_params = $raw_params;
    }

    /**
     * @return array
     */
    public function headers()
    {
        if ($this->headers) {
            return $this->headers;
        }

        return $this->headers = $this->raw_request->header;
    }

    /**
     * @param string $name
     * @param null $default
     *
     * @return mixed
     */
    public function header(string $name, $default = null)
    {
        return array_key_or_default($name, $this->headers(), $default);
    }

    /**
     * @return array
     */
    public function params()
    {
        if ($this->params) {
            return $this->params;
        }

        return $this->params = array_merge(
            $this->raw_params,
            $this->raw_request->get ?: []
        );
    }

    /**
     * @param string $name
     * @param null $default
     *
     * @return mixed
     */
    public function param(string $name, $default = null)
    {
        return array_key_or_default($name, $this->params(), $default);
    }

    /**
     * @return array
     */
    public function servers()
    {
        if ($this->servers) {
            return $this->servers;
        }

        return $this->servers = $this->raw_request->server;
    }

    /**
     * @param string $name
     * @param null $default
     *
     * @return mixed
     */
    public function server(string $name, $default = null)
    {
        return array_key_or_default($name, $this->servers(), $default);
    }

    /**
     * @return array
     */
    public function cookies()
    {
        if ($this->cookies) {
            return $this->cookies;
        }

        return $this->cookies = $this->raw_request->cookie;
    }

    /**
     * @param string $name
     * @param null $default
     *
     * @return mixed
     */
    public function cookie(string $name, $default = null)
    {
        return array_key_or_default($name, $this->cookies(), $default);
    }

    /**
     * @return array
     */
    public function all()
    {
        return [
            'headers' => $this->headers(),
            'params' => $this->params(),
            'servers' => $this->servers(),
            'cookies' => $this->cookies(),
        ];
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this->all());
    }
}
