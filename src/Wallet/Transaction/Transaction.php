<?php

namespace ManojX\TronBundle\Wallet\Transaction;

use ManojX\TronBundle\Exception\TronAddressException;
use ManojX\TronBundle\Exception\TronException;
use ManojX\TronBundle\Exception\TronTransactionException;
use ManojX\TronBundle\Node\Node;
use ManojX\TronBundle\Utils\Trx;
use ManojX\TronBundle\Wallet\Address\Address;
use ManojX\TronBundle\Wallet\Wallet;

class Transaction implements TransactionInterface
{
    use TransactionTrait;

    private Wallet $wallet;

    private Node $node;

    private ?string $to = null;

    private ?string $from = null;

    private ?float $amount = 0;

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
     *
     * @return self
     */
    public function setTo(?string $to): self
    {
        $this->to = $to;
        return $this;
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
     *
     * @return self
     */
    public function setFrom(?string $from): self
    {
        $this->from = $from;

        return $this;
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
     *
     * @return self
     */
    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Set the wallet instance for the current context.
     * @param Wallet $wallet The Wallet instance to be set.
     *
     * @return self
     */
    public function setWallet(Wallet $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * Set the node instance for blockchain interactions.
     *
     * @param Node $node The Node instance to be set.
     * @return self
     */
    public function setNode(Node $node): self
    {
        $this->node = $node;

        return $this;
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

    /**
     * Retrieves transaction information based on the provided transaction hash.
     *
     * This method checks if the provided hash is valid. If valid, it retrieves the transaction information
     * from the blockchain node. It can return the raw response or a formatted version of the transaction data
     * based on the `raw` parameter.
     *
     * @param string $hash The transaction hash to look up.
     * @param bool $raw Optional. If true, returns the raw response from the node; otherwise, returns
     *                  formatted transaction data. Defaults to false.
     *
     * @throws TronTransactionException|TronException if the transaction hash is invalid or if there is an error
     *                                  retrieving the transaction information.
     *
     * @return array The transaction information, which includes the success status, formatted data, or error message.
     */
    public function getByHash(string $hash, bool $raw = false): array
    {
        if (!self::isValidTransactionHash($hash)) {
            throw new TronTransactionException('Invalid transaction hash.');
        }

        $node = $this->node->setNodeToUse(Node::EXPLORER);
        $response = $node->getTransactionInfoByHash($hash);

        if (!$response['success']) {
            throw new TronTransactionException($response['error']['message']);
        }

        if ($raw) {
            return $response;
        }

        $response['data'] = $this->formatTransactionOfExplorer($response['data']);
        return $response;
    }

    /**
     * Retrieves transaction details based on the provided address and transaction hash.
     *
     * This method first calls `getByHash` to retrieve the transaction information for the specified hash.
     * It then checks if the specified address is involved in the transaction (either as the sender or receiver).
     * If found, it categorizes the action as 'withdraw' or 'deposit' based on the address role in the transaction.
     *
     * @param string $address The address to check for involvement in the transaction.
     * @param string $hash The transaction hash to look up.
     *
     * @throws TronTransactionException|TronException if there is an error retrieving the transaction information.
     *
     * @return array|null Returns transaction details if the address is involved; otherwise, returns null.
     */
    public function getByAddressAndHash(string $address, string $hash): ?array
    {
        $response = $this->getByHash($hash);
        if (!$response['success']) {
            throw new TronTransactionException($response['error']['message']);
        }

        $transaction = $response['data'];

        if ($transaction['type'] === 'transfer') {
            if (in_array($address, [$transaction['fromAddr'], $transaction['toAddr']])) {
                $transaction['action'] = $address === $transaction['fromAddr'] ? 'withdraw' : 'deposit';
                return $transaction;
            }
        }

        $transferForAddress = null;
        if (isset($transaction['transfers'])) {
            foreach ($transaction['transfers'] as $transfer) {
                if (in_array($address, [$transfer['fromAddr'], $transfer['toAddr']])) {
                    $transfer['action'] = $address === $transfer['fromAddr'] ? 'withdraw' : 'deposit';
                    $transferForAddress = $transfer;
                }
            }
        }

        if ($transferForAddress) {
            unset($transaction['transfers']);
            return array_merge($transaction, $transferForAddress);
        }
        return null;
    }
}
