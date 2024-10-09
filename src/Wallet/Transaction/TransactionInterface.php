<?php

namespace ManojX\TronBundle\Wallet\Transaction;

use ManojX\TronBundle\Exception\TronAddressException;
use ManojX\TronBundle\Exception\TronException;
use ManojX\TronBundle\Exception\TronTransactionException;
use ManojX\TronBundle\Node\Node;
use ManojX\TronBundle\Wallet\Wallet;

interface TransactionInterface
{
    /**
     * Get the recipient address.
     *
     * @return string|null The recipient address.
     */
    public function getTo(): ?string;

    /**
     * Set the recipient address.
     *
     * @param string|null $to The address to send funds to.
     *
     * @return self
     */
    public function setTo(?string $to): self;

    /**
     * Get the sender address.
     *
     * @return string|null The sender address.
     */
    public function getFrom(): ?string;

    /**
     * Set the sender address.
     *
     * @param string|null $from The address sending funds.
     *
     * @return self
     */
    public function setFrom(?string $from): self;

    /**
     * Get the transaction amount.
     *
     * @return float|null The amount of the transaction.
     */
    public function getAmount(): ?float;

    /**
     * Set the transaction amount.
     *
     * @param float|null $amount The amount to be sent in the transaction.
     *
     * @return self
     */
    public function setAmount(?float $amount): self;

    /**
     * Set the wallet instance for the current context.
     *
     * This method assigns the provided Wallet instance to the current object,
     * allowing subsequent operations to utilize this wallet.
     *
     * @param Wallet $wallet The Wallet instance to be set.
     *
     * @return self
     */
    public function setWallet(Wallet $wallet): self;

    /**
     * Set the node instance for blockchain interactions.
     *
     * This method assigns the provided Node instance to the current object,
     * enabling communication with the blockchain for various operations.
     *
     * @param Node $node The Node instance to be set.
     *
     * @return self
     */
    public function setNode(Node $node): self;

    /**
     * Create a transaction.
     *
     * @return array The response from the node after creating the transaction.
     * @throws TronAddressException If the addresses are invalid.
     * @throws TronTransactionException If the transaction creation fails.
     */
    public function create(): array;

    /**
     * Create and sign a transaction.
     *
     * Combines transaction creation and signing into one method.
     *
     * @return array The signed transaction data.
     * @throws TronTransactionException If the transaction creation or signing fails.
     * @throws TronAddressException If the addresses are invalid.
     */
    public function createAndSign(): array;


    /**
     * Retrieve a transaction by its hash.
     *
     * This method fetches the details of a transaction from the blockchain using the provided hash.
     *
     * @param string $hash The hash of the transaction or block to retrieve.
     * @param bool $raw Optional. If true, return the raw data without any processing.
     *
     * @return array An array containing the details of the transaction.
     *
     * @throws TronTransactionException|TronException If there is an issue retrieving the data from the blockchain.
     */
    public function getByHash(string $hash, bool $raw = false): array;

    /**
     * Retrieves transaction details based on the provided address and transaction hash.
     *
     * @param string $address The address to check for involvement in the transaction.
     * @param string $hash The transaction hash to look up.
     *
     * @throws TronTransactionException|TronException if there is an error retrieving the transaction information.
     *
     * @return array|null Returns transaction details if the address is involved; otherwise, returns null.
     */
    public function getByAddressAndHash(string $address, string $hash): ?array;
}
