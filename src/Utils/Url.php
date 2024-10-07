<?php

namespace ManojX\TronBundle\Utils;

class Url
{
    /**
     * Check if the given URL is valid
     * @param $url
     * @return bool
     */
    public static function isValidUrl($url): bool
    {
        return (bool)parse_url($url);
    }
}