<?php

namespace ManojX\TronBundle\Lib;

class Hash
{
    /**
     * SHA256 hash
     * @param string $data
     * @param bool $raw
     * @return string
     */
    public static function SHA256(string $data, bool $raw = true): string
    {
        return hash('sha256', $data, $raw);
    }
}