<?php

namespace ManojX\Examples;

use ManojX\TronBundle\TronInterface;
use ManojX\TronBundle\Wallet\Address\Address;

class Wallet
{
    private TronInterface $tron;

    public function __construct(TronInterface $tron)
    {
        $this->tron = $tron;
    }

    public function createAddress()
    {

        $wallet = $this->tron->getWallet();

        $address = Address::create();

        echo '<pre>';
        echo '<b/>Address:</b> ' . $address->getAddress() . '<br/>';
        echo '<b/>Address Hex:</b> ' . $address->getAddressHex() . '<br/>';
        echo '<b/>Public Key:</b> ' . $address->getPublicKey() . '<br/>';
        echo '<b/>Private Key:</b> ' . $address->getPrivateKey() . '<br/>';
        echo '</pre>';
        die;
    }
}