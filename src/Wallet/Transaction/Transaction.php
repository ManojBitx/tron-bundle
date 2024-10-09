<?php

namespace ManojX\TronBundle\Wallet\Transaction;

use ManojX\TronBundle\Exception\TronAddressException;
use ManojX\TronBundle\Exception\TronTransactionException;
use ManojX\TronBundle\Utils\Trx;
use ManojX\TronBundle\Wallet\Address\Address;
use ManojX\TronBundle\Wallet\Wallet;

class Transaction implements TransactionInterface
{
    private Wallet $wallet;

    private ?string $to = null;

    private ?string $from = null;

    private ?float $amount = 0;

    /**
     * Initializes a Transaction with the given wallet.
     *
     * @param Wallet $wallet The Wallet instance used for transaction operations.
     */
    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    /**
     * Get the recipient address.
     *
     * @return string|null The recipient address.
     */
    public function getTo(): ?string
    {
        return $this->to;
    }

    /**
     * Set the recipient address.
     *
     * @param string|null $to The address to send funds to.
     */
    public function setTo(?string $to): void
    {
        $this->to = $to;
    }

    /**
     * Get the sender address.
     *
     * @return string|null The sender address.
     */
    public function getFrom(): ?string
    {
        return $this->from;
    }

    /**
     * Set the sender address.
     *
     * @param string|null $from The address sending funds.
     */
    public function setFrom(?string $from): void
    {
        $this->from = $from;
    }

    /**
     * Get the transaction amount.
     *
     * @return float|null The amount of the transaction.
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * Set the transaction amount.
     *
     * @param float|null $amount The amount to be sent in the transaction.
     */
    public function setAmount(?float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * Create a transaction.
     *
     * Validates and creates a transaction payload.
     *
     * @return array The response from the node after creating the transaction.
     * @throws TronAddressException If the addresses are invalid.
     * @throws TronTransactionException If the transaction creation fails.
     */
    public function create(): array
    {
        if ($this->getTo() === null) {
            throw new TronTransactionException('To address is required.');
        }

        if ($this->getAmount() === null || $this->getAmount() <= 0) {
            throw new TronTransactionException('Amount is required.');
        }

        $toHex = Address::base58ToHex($this->getTo());

        $from = $this->getFrom();
        if (!$from) {
            $from = $this->wallet->getAddress();
        }
        $fromHex = Address::base58ToHex($from);
        if ($toHex === $fromHex) {
            throw new TronTransactionException('From and To address cannot be the same.');
        }

        $payload = [
            'to_address' => $toHex,
            'owner_address' => $fromHex,
            'amount' => Trx::toSun($this->getAmount()),
        ];

        $node = $this->wallet->getNode();

        $response = $node->createTransaction($payload);
        if (!$response['success']) {
            throw new TronTransactionException($response['error']['message']);
        }

        return $response;
    }

    /**
     * Create and sign a transaction.
     *
     * Combines transaction creation and signing into one method.
     *
     * @return array The signed transaction data.
     * @throws TronTransactionException If the transaction creation or signing fails.
     * @throws TronAddressException If the addresses are invalid.
     */
    public function createAndSign(): array
    {
        $response = $this->create();
        return $this->wallet->signTransaction($response['data']);
    }
}
