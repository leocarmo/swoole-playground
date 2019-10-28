<?php

if (! function_exists('env')) {
    /**
     * @param string $key
     * @param null $default
     * @return array|bool|false|null|string
     */
    function env(string $key, $default = null)
    {
        $value = getenv($key);

        if (! $value) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
            default:
                return $value;
        }
    }
}

if (! function_exists('array_key_or_default')) {
    /**
     * @param string $key
     * @param $array
     * @param null $default
     * @return mixed
     */
    function array_key_or_default(string $key, $array, $default = null)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        return $default;
    }
}

if (! function_exists('response')) {
    /**
     * @param array $content
     * @param int $status
     * @param array $headers
     *
     * @return \App\Http\Response
     */
    function response($content = [], $status = 200, array $headers = [])
    {
        return new \App\Http\Response($content, $status, $headers);
    }
}