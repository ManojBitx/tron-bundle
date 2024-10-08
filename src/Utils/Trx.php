<?php

namespace ManojX\TronBundle\Utils;

class Trx
{
    const TRX_TO_SUN = 6;

    const NULL_ADDRESS = '410000000000000000000000000000000000000000';

    /**
     * Convert TRX to SUN
     *
     * @param float $amount Amount in TRX
     *
     * @return int Amount in SUN
     */
    public static function toSun(float $amount, int $decimal = self::TRX_TO_SUN): int
    {
        $scale = pow(10, $decimal);
        return (int)bcmul((string)$amount, (string)$scale, 0);
    }

    /**
     * Convert SUN to TRX
     *
     * @param int $amount Amount in SUN
     *
     * @return float Amount in TRX
     */
    public static function fromSun(int $amount, int $decimal = self::TRX_TO_SUN): float
    {
        $scale = pow(10, $decimal);
        return (float)bcdiv((string)$amount, (string)$scale, 8);
    }

}
