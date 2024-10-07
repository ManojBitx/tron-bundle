<?php

namespace ManojX\TronBundle\Node;

interface NodeInterface
{
    /**
     * @return array
     */
    public function getCurrentBlock(): array;

    /**
     * @param array $signedTransaction
     * @return array
     */
    public function broadcastTransaction(array $signedTransaction): array;

    /**
     * @param array $transaction
     * @return array
     */
    public function createTransaction(array $transaction): array;
}
