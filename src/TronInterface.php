<?php

namespace ManojX\TronBundle;

use ManojX\TronBundle\Exception\TronException;
use ManojX\TronBundle\Node\NodeInterface;
use ManojX\TronBundle\Wallet\Wallet;

interface TronInterface
{
    /**
     * Get the network name or configuration.
     *
     * @param bool $config If true, returns the network configuration; otherwise, returns the network name.
     * @return mixed The current network or its configuration.
     */
    public function getNetwork(bool $config = false): mixed;

    /**
     * Set the network from the configured networks.
     *
     * Validates and sets the specified network as the current network.
     *
     * @param string $network The name of the network to set.
     * @throws TronException If the network configuration is invalid.
     */
    public function setNetwork(string $network): void;

    /**
     * Get the node instance used for blockchain interactions.
     *
     * @return NodeInterface The node instance for blockchain operations.
     */
    public function getNode(): NodeInterface;

    /**
     * Get a wallet instance for the current network.
     *
     * @param string|null $privateKey Optional private key for the wallet.
     * @return Wallet A new Wallet instance associated with the current network.
     */
    public function getWallet(?string $privateKey = null): Wallet;

    /**
     * Send a raw transaction to the blockchain.
     *
     * @param array $signedTransaction The signed transaction data to be sent.
     * @return array The response from the node after broadcasting the transaction.
     */
    public function sendRawTransaction(array $signedTransaction): array;
}
