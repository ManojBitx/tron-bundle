<?php

namespace ManojX\TronBundle\Utils;

class Arr
{
    /**
     *  Check if the given value is an array
     *
     * @param $array
     * @return bool
     */
    public static function isArray($array): bool
    {
        return is_array($array);
    }

}