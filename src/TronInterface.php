<?php

namespace ManojX\TronBundle;

use ManojX\TronBundle\Exception\TronException;
use ManojX\TronBundle\Node\NodeInterface;
use ManojX\TronBundle\Wallet\Wallet;

interface TronInterface
{

    /**
     * Get the network name or configuration
     *
     * @param bool $config Return the network configuration
     *
     * @return mixed
     */
    public function getNetwork(bool $config = false): mixed;


    /**
     * Set the network from the configured networks
     *
     * @throws TronException
     */
    public function setNetwork(string $network): void;

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
