<?php

namespace ManojX\TronBundle\Utils;

class Num
{
    /**
     * Check if the given value is a negative number
     *
     * @param string $value
     * @return bool
     */
    public static function isNegative(string $value): bool
    {
        return (strpos($value, '-') === 0);
    }
}