<?php

namespace ManojX\TronBundle;

use ManojX\TronBundle\Node\NodeInterface;
use ManojX\TronBundle\Wallet\Wallet;

interface TronInterface
{
    /**
     * Get the node instance
     * @return NodeInterface
     */
    public function getNode(): NodeInterface;

    /**
     * Get the wallet instance
     * @param string|null $privateKey
     * @return Wallet
     */
    public function getWallet(?string $privateKey = null): Wallet;
}
