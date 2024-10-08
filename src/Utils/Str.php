<?php

namespace ManojX\TronBundle\Utils;

class Str
{
    /**
     * Check if the given value is a hex string
     *
     * @param $str
     * @return bool
     */
    public static function isHex($str): bool
    {
        return is_string($str) && ctype_xdigit($str);
    }

    /**
     * Check if the given hex string is prefixed with '0x'
     *
     * @param string $str
     *
     * @return bool
     */
    public static function isHexPrefixed(string $str): bool
    {
        return substr($str, 0, 2) === '0x';
    }

    /**
     * Clean the given string from any non-alphanumeric characters
     *
     * @param string $str
     * @param bool $removeAllSpace
     * @return string
     */
    public static function sanitize(string $str, bool $removeAllSpace = false): string
    {
        $regex = $removeAllSpace ? '/[^\w.-]/' : '/[^\w\s.-]/';
        return preg_replace($regex, '', trim($str));
    }
}