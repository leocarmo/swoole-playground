<?php

namespace App\Support;

trait ApplicationPatch
{

    /**
     * @var string
     */
    protected static $base_patch;

    /**
     * Returns storage application path
     *
     * @param string|null $path
     * @return string
     */
    public static function storagePath(string $path = null)
    {
        return self::$base_patch . '/storage/' . $path;
    }
}