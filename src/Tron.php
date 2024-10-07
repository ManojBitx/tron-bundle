<?php

namespace ManojX\TronBundle;

use ManojX\TronBundle\Exception\TronAddressException;
use ManojX\TronBundle\Exception\TronException;
use ManojX\TronBundle\Node\Node;
use ManojX\TronBundle\Node\NodeInterface;
use ManojX\TronBundle\Wallet\Wallet;

class Tron implements TronInterface
{
    private NodeInterface $node;

    /**
     * @throws TronException
     */
    public function __construct(array $httpConfig)
    {
        $this->node = new Node($httpConfig);
    }

    /**
     * Get the node instance
     */
    public function getNode(): NodeInterface
    {
        return $this->node;
    }

    /**
     * Get a wallet instance
     *
     * @throws TronAddressException
     */
    public function getWallet(?string $privateKey = null): Wallet
    {
        return new Wallet($privateKey, $this->node);
    }

    public function sendRawTransaction(array $signedTransaction): array
    {
        return $this->node->broadcastTransaction($signedTransaction);
    }
}
