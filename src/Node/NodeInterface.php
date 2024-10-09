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

    /**
     * @param array $contract
     * @return array
     */
    public function triggerConstantContract(array $contract): array;

    /**
     * @param array $contract
     * @return array
     */
    public function triggerSmartContract(array $contract): array;

    /**
     * @param string $ownerAddress
     * @param string $authorizedAddress
     * @param string $operations
     * @return array
     */
    public function accountPermissionUpdate(string $ownerAddress, string $authorizedAddress, string $operations): array;
}
