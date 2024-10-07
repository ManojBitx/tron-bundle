<?php

namespace ManojX\TronBundle\Utils;

class Tron
{
    const CONVERSION_FACTOR = 1e6;

    /**
     * Convert TRX to SUN
     *
     * @param float $amount Amount in TRX
     *
     * @return int Amount in SUN
     */
    public static function toSun(float $amount): int
    {
        return (int)bcmul((string)$amount, (string)self::CONVERSION_FACTOR, 0);
    }

    /**
     * Convert SUN to TRX
     *
     * @param int $amount Amount in SUN
     *
     * @return float Amount in TRX
     */
    public static function fromSun(int $amount): float
    {
        return (float)bcdiv((string)$amount, (string)self::CONVERSION_FACTOR, 8);
    }
}
