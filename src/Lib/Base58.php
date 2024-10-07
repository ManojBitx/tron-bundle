<?php

namespace ManojX\TronBundle\Lib;

class Base58
{

    public static function encode(string $hex): string
    {
        $hexBin = hex2bin($hex);
        $hash = Hash::SHA256(Hash::SHA256($hexBin));
        $checksum = substr($hash, 0, 4);
        $checksum = $hexBin . $checksum;

        return Crypto::toString(Crypto::bin2bc($checksum));
    }

    /**
     * Convert a hexadecimal string to Base58
     *
     * @param string $hex
     * @param int $removeTrailingBytes
     *
     * @return string
     */
    public static function decode(string $hex, int $removeTrailingBytes = 4): string
    {
        $address = bin2hex(Crypto::bc2bin(Crypto::toDecimal($hex)));

        if ($removeTrailingBytes) {
            $address = substr($address, 0, -($removeTrailingBytes * 2));
        }

        return $address;
    }

}