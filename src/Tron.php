<?php

namespace ManojX\TronBundle;

use ManojX\TronBundle\Exception\TronAddressException;
use ManojX\TronBundle\Exception\TronException;
use ManojX\TronBundle\Node\Node;
use ManojX\TronBundle\Node\NodeInterface;
use ManojX\TronBundle\Wallet\Wallet;

class Tron implements TronInterface
{

    private string $network;

    private array $networks;

    private NodeInterface $node;

    /**
     * @throws TronException
     */
    public function __construct(string $defaultNetwork, array $networks)
    {
        $this->network = $defaultNetwork;
        $this->networks = $networks;

        $this->setNetwork($this->network);
    }

    public function getNetwork(bool $config = false): mixed
    {
        if ($config) {
            return $this->networks[$this->network];
        }

        return $this->network;
    }

    /**
     * Set the network configuration
     *
     * @throws TronException
     */
    public function setNetwork(string $network): void
    {
        if (isset($this->networks[$network])) {
            $config = $this->networks[$network];
        } else {
            throw new \InvalidArgumentException(sprintf('Network configuration for "%s" not found.', $network));
        }

        $this->network = $network;
        $httpConfig = $config['http'] ?? [];
        $this->node = new Node($httpConfig, $network);
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
