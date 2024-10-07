<?php

namespace ManojX\TronBundle\Wallet\Address;

use kornrunner\Keccak;
use ManojX\TronBundle\Exception\TronAddressException;
use ManojX\TronBundle\Lib\Base58;
use ManojX\TronBundle\Lib\Hash;

class Address implements AddressInterface
{

    const ADDRESS_SIZE = 34;

    const ADDRESS_PREFIX = "41";

    const ADDRESS_PREFIX_BYTE = 0x41;

    private string $address;

    private string $addressHex;

    private string $publicKey;

    private string $privateKey;

    /**
     * @throws TronAddressException
     */
    public function __construct(array $data)
    {
        $this->publicKey = $data['public_key'];
        $this->privateKey = $data['private_key'];

        $this->addressHex = self::publicKeyToHex($this->publicKey);
        $this->address = self::hexToBase58($this->addressHex);
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getAddressHex(): string
    {
        return $this->addressHex;
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * Convert a public key to a Tron address
     *
     * @param string $publicKey
     * @return string
     *
     * @throws TronAddressException
     */
    public static function publicKeyToHex(string $publicKey): string
    {
        $publicKeyBin = hex2bin($publicKey);
        if (strlen($publicKeyBin) == 65) {
            $publicKeyBin = substr($publicKeyBin, 1);
        }

        try {
            $hash = Keccak::hash($publicKeyBin, 256);
        } catch (\Exception $e) {
            throw new TronAddressException("Failed to hash public key.");
        }

        return self::ADDRESS_PREFIX . substr($hash, 24);
    }


    /**
     * Convert a hexadecimal string to Base58
     *
     * @param string $hex
     * @return string
     *
     * @throws TronAddressException
     */
    public static function hexToBase58(string $hex): string
    {
        if (!ctype_xdigit($hex)) {
            throw new TronAddressException("Invalid hexadecimal string.");
        }

        if (strlen($hex) < 2 || strlen($hex) % 2 !== 0) {
            throw new TronAddressException("Invalid hex string length.");
        }

        return Base58::encode($hex);
    }

    /**
     * Convert a Base58 string to hexadecimal
     *
     * @param string $address
     * @return string
     *
     */
    public static function base58ToHex(string $address): string
    {
        if (strlen($address) == 42 && mb_strpos($address, self::ADDRESS_PREFIX) == 0) {
            return $address;
        }

        return Base58::decode($address);
    }

    /**
     * Validate a Tron address
     *
     * @param string $address
     * @return bool
     *
     */
    public static function isValid(string $address): bool
    {
        if (strlen($address) !== self::ADDRESS_SIZE) {
            return false;
        }

        $address = Base58::decode($address, 0);

        $utf8 = hex2bin($address);
        if (strlen($utf8) !== 25) {
            return false;
        }

        if (strpos($utf8, chr(self::ADDRESS_PREFIX_BYTE)) !== 0) {
            return false;
        }

        $checksum = substr($utf8, 21);

        $address = substr($utf8, 0, 21);
        $hash = Hash::SHA256(Hash::SHA256($address));
        $expectedChecksum = substr($hash, 0, 4);

        if ($checksum === $expectedChecksum) {
            return true;
        }

        return false;
    }

}