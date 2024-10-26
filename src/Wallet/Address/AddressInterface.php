<?php

namespace ManojX\TronBundle\Wallet\Address;

use ManojX\TronBundle\Exception\TronAddressException;

interface AddressInterface
{
    public static function create(): Address;

    public function getAddress(): ?string;

    public function getAddressHex(): ?string;

    public function getPublicKey(): ?string;

    public function getPrivateKey(): ?string;

    /**
     * @throws TronAddressException
     */
    public static function publicKeyToHex(string $publicKey): string;


    /**
     * Convert a hexadecimal string to Base58
     *
     * @param string $hex
     * @return string
     *
     * @throws TronAddressException
     */
    public static function hexToBase58(string $hex): string;

    /**
     * Convert a Base58 string to hexadecimal
     *
     * @param string $address
     * @return string
     *
     */
    public static function base58ToHex(string $address): string;

    /**
     * Check if an address is valid
     *
     * @param string $address
     * @return bool
     */
    public static function isValid(string $address): bool;

}