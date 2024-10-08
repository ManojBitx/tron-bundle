<?php

namespace ManojX\TronBundle\Contract\Token;

use ManojX\TronBundle\Contract\TRC20;
use ManojX\TronBundle\Exception\TronException;

class USDT extends TRC20
{
    const MAINNET = 'mainnet';

    const SHASTA = 'shasta';

    const NILE = 'nile';

    const CONTRACT = [
        self::MAINNET => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t',
        self::SHASTA => 'TG3XXyExBkPp9nzdajDZsozEu4BkaSJozs',
        self::NILE => 'TXYZopYRdj2D9XRtbG411XZZ3kM5VkAeBf',
    ];

    public function __construct(string $network, ?string $contractAddress = null, ?array $abi = null)
    {
        if (!isset(self::CONTRACT[$network]) && $contractAddress === null) {
            throw new TronException('Contract address not found for ' . $network . ' network. Please provide contract address.');
        }
        $contractAddress = self::CONTRACT[$network];
        parent::__construct($contractAddress, $abi);
    }

    /**
     * Get the balance of the wallet
     *
     * @return string
     *
     * @throws TronException
     */
    public function balance(): string
    {
        $wallet = $this->getWallet();
        return $this->getBalanceOf($wallet->getAddress());
    }
}