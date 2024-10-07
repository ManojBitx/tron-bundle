<?php

namespace ManojX\TronBundle\Wallet;

use ManojX\TronBundle\Exception\TronAddressException;
use ManojX\TronBundle\Wallet\Address\Address;

interface WalletInterface
{

    /**
     * Create a new address
     *
     * @return Address
     *
     * @throws TronAddressException
     */
    public function createNewAddress(): Address;

}