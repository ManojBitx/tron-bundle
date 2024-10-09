<?php

namespace ManojX\TronBundle\Wallet\Transaction;

use ManojX\TronBundle\Exception\TronAddressException;
use ManojX\TronBundle\Exception\TronTransactionException;

interface TransactionInterface
{
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
     * Get the recipient address.
     *
     * @return string|null The recipient address.
     */
    public function getTo(): ?string;

    /**
     * Set the recipient address.
     *
     * @param string|null $to The address to send funds to.
     */
    public function setTo(?string $to): void;

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
     */
    public function setFrom(?string $from): void;

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
     */
    public function setAmount(?float $amount): void;
}
