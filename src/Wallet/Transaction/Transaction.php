<?php

namespace ManojX\TronBundle\Wallet\Transaction;

use ManojX\TronBundle\Exception\TronAddressException;
use ManojX\TronBundle\Exception\TronTransactionException;
use ManojX\TronBundle\Utils\Trx;
use ManojX\TronBundle\Wallet\Address\Address;
use ManojX\TronBundle\Wallet\Wallet;

class Transaction
{

    private Wallet $wallet;

    private ?string $to = null;

    private ?float $amount = 0;

    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    /**
     *
     * Create a transaction
     *
     * @return array
     *
     * @throws TronAddressException
     * @throws TronTransactionException
     *
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
        $fromHex = $this->wallet->getAddress(true);
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
     * Create and sign a transaction
     *
     * @throws TronTransactionException
     * @throws TronAddressException
     */
    public function createAndSign(): array
    {
        $response = $this->create();
        return $this->wallet->signTransaction($response['data']);
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function setTo(?string $to): void
    {
        $this->to = $to;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): void
    {
        $this->amount = $amount;
    }
}