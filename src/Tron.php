<?php

namespace ManojX\TronBundle;

use ManojX\TronBundle\Exception\TronAddressException;
use ManojX\TronBundle\Exception\TronException;
use ManojX\TronBundle\Node\Node;
use ManojX\TronBundle\Node\NodeInterface;
use ManojX\TronBundle\Wallet\Transaction\Transaction;
use ManojX\TronBundle\Wallet\Wallet;

class Tron implements TronInterface
{
    private string $network;

    private array $networks;

    private NodeInterface $node;

    /**
     *
     * Initializes the Tron instance with a default network and available networks.
     *
     * @param string $defaultNetwork The default network to be used.
     * @param array $networks An array of available network configurations.
     *
     * @throws TronException If the network configuration is invalid.
     */
    public function __construct(string $defaultNetwork, array $networks)
    {
        $this->network = $defaultNetwork;
        $this->networks = $networks;

        $this->setNetwork($this->network);
    }

    /**
     * Get the current network or its configuration.
     *
     * @param bool $config If true, returns the network configuration; otherwise, returns the network name.
     * @return mixed The current network or its configuration.
     */
    public function getNetwork(bool $config = false): mixed
    {
        if ($config) {
            return $this->networks[$this->network];
        }

        return $this->network;
    }

    /**
     * Set the network configuration.
     *
     * Validates and sets the specified network as the current network.
     *
     * @param string $network The name of the network to set.
     * @throws TronException If the network configuration is invalid.
     */
    public function setNetwork(string $network): void
    {
        if (isset($this->networks[$network])) {
            $config = $this->networks[$network];
        } else {
            throw new \InvalidArgumentException(sprintf('Network configuration for "%s" not found.', $network));
        }

        $this->network = $network;
        $this->node = new Node($config, $network);
    }

    /**
     * Get the node instance used for blockchain interactions.
     *
     * @return NodeInterface The node instance.
     */
    public function getNode(): NodeInterface
    {
        return $this->node;
    }

    /**
     * Get a wallet instance for the current network.
     *
     * @param string|null $privateKey Optional private key for the wallet.
     * @return Wallet A new Wallet instance.
     * @throws TronAddressException If there is an issue with the wallet address.
     */
    public function getWallet(?string $privateKey = null): Wallet
    {
        return new Wallet($privateKey, $this->node);
    }

    /**
     * Create a new Transaction instance with the current Node connection.
     *
     * @return Transaction A new Transaction instance with Node connection.
     */
    public function transaction(): Transaction
    {
        $transaction = new Transaction();
        $transaction->setNode($this->node);
        return $transaction;
    }

    /**
     * Send a raw transaction to the blockchain.
     *
     * @param array $signedTransaction The signed transaction data to be sent.
     * @return array The response from the node after broadcasting the transaction.
     */
    public function sendRawTransaction(array $signedTransaction): array
    {
        return $this->node->broadcastTransaction($signedTransaction);
    }
}
