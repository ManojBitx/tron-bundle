<?php

namespace ManojX\TronBundle\Lib;

class Base58
{

    /**
     * Convert hexadecimal to Base58
     *
     * @param string $base58
     *
     * @return string
     */
    public static function encode(string $base58): string
    {
        $base58Bin = hex2bin($base58);
        $hash = Hash::SHA256(Hash::SHA256($base58Bin));
        $checksum = substr($hash, 0, 4);
        $checksum = $base58Bin . $checksum;

        return Crypto::toString(Crypto::bin2bc($checksum));
    }

    /**
     * Convert Base58 to hexadecimal
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