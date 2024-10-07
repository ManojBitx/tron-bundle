<?php

namespace ManojX\TronBundle\Lib;

class Crypto
{
    /**
     * Convert a large integer to a base58 string
     *
     * @param string $numbers
     * @param int $length
     *
     * @return string
     */
    public static function toString(string $numbers, int $length = 58): string
    {
        return Crypto::dec2base($numbers, $length, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
    }

    /**
     * Convert a base58 string to a large integer
     *
     * @param string $string
     * @param int $length
     *
     * @return string
     */
    public static function toDecimal(string $string, int $length = 58): string
    {
        return Crypto::base2dec($string, $length, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
    }

    /**
     * Convert a decimal number to a binary number
     *
     * @param string $num
     * @return string
     */
    public static function bc2bin(string $num): ?string
    {
        return self::dec2base($num, 256);
    }

    /**
     * Convert a binary number to a decimal number
     *
     * @param string $num
     *
     * @return string
     */
    public static function bin2bc(string $num): string
    {
        return self::base2dec($num, 256);
    }

    /**
     * Convert a decimal number to a base number
     *
     * @param string $dec
     * @param int $base
     * @param string|bool $digits
     *
     * @return string
     */
    public static function dec2base(string $dec, int $base, $digits = false): string
    {
        if (extension_loaded('bcmath')) {
            if ($base < 2 || $base > 256) {
                die("Invalid Base: " . $base);
            }
            bcscale(0);
            $value = "";

            if (!$digits) {
                $digits = self::digits($base);
            }
            while ($dec > $base - 1) {
                $rest = bcmod($dec, $base);
                $dec = bcdiv($dec, $base);
                $value = $digits[$rest] . $value;
            }
            return $digits[intval($dec)] . $value;
        } else {
            die('Please install BCMATH');
        }
    }

    /**
     * Convert a base number to a decimal number
     *
     * @param string $value
     * @param int $base
     * @param string|bool $digits
     *
     * @return string
     */
    public static function base2dec(string $value, int $base, $digits = false): string
    {
        if (extension_loaded('bcmath')) {
            if ($base < 2 || $base > 256) {
                die("Invalid Base: " . $base);
            }
            bcscale(0);
            if ($base < 37) {
                $value = strtolower($value);
            }

            if (!$digits) {
                $digits = self::digits($base);
            }
            $size = strlen($value);
            $dec = "0";
            for ($loop = 0; $loop < $size; $loop++) {
                $element = strpos($digits, $value[$loop]);
                $power = bcpow($base, $size - $loop - 1);
                $dec = bcadd($dec, bcmul($element, $power));
            }
            return $dec;
        } else {
            die('Please install BCMATH');
        }
    }

    /**
     * Get the digits for a base number
     *
     * @param int $base
     *
     * @return string
     */
    public static function digits(int $base): string
    {
        if ($base > 64) {
            $digits = "";
            for ($loop = 0; $loop < 256; $loop++) {
                $digits .= chr($loop);
            }
        } else {
            $digits = "0123456789abcdefghijklmnopqrstuvwxyz";
            $digits .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ-_";
        }
        return substr($digits, 0, $base);
    }

}