<?php

namespace ManojX\TronBundle\Node;

interface NodeInterface
{
    /**
     * Retrieve the current block information.
     *
     * @return array An array containing details about the current block.
     */
    public function getCurrentBlock(): array;

    /**
     * Broadcast a signed transaction to the network.
     *
     * @param array $signedTransaction The signed transaction data to be broadcasted.
     * @return array An array containing the response from the network.
     */
    public function broadcastTransaction(array $signedTransaction): array;

    /**
     * Create a transaction with the given parameters.
     *
     * @param array $transaction The transaction data to be created.
     * @return array An array containing the response from the node after creating the transaction.
     */
    public function createTransaction(array $transaction): array;

    /**
     * Trigger a constant smart contract function.
     *
     * @param array $contract The contract details for the constant function call.
     * @return array An array containing the response from the contract execution.
     */
    public function triggerConstantContract(array $contract): array;

    /**
     * Trigger a smart contract function.
     *
     * @param array $contract The contract details for the function call.
     * @return array An array containing the response from the contract execution.
     */
    public function triggerSmartContract(array $contract): array;

    /**
     * Update the account permissions for the given owner address.
     *
     * @param string $ownerAddress The address of the owner whose permissions are to be updated.
     * @param string $authorizedAddress The address to be authorized.
     * @param string $operations The type of operations to allow for the authorized address.
     * @return array An array containing the response from the permission update operation.
     */
    public function accountPermissionUpdate(string $ownerAddress, string $authorizedAddress, string $operations): array;

    /**
     * Retrieve transaction details by transaction hash.
     *
     * This method fetches the details of a transaction from the blockchain
     * using the provided transaction hash from the explorer API.
     *
     * @param string $hash The transaction Hash of the transaction to retrieve.
     * @return array An array containing the details of the transaction.
     */
    public function getTransactionInfoByHash(string $hash): array;

    /**
     * Retrieve transaction details by transaction ID.
     *
     * This method fetches the details of a transaction from the blockchain
     * using the provided transaction ID.
     *
     * @param string $txID The transaction ID of the transaction to retrieve.
     * @return array An array containing the details of the transaction.
     */
    public function getTransactionById(string $txID): array;

    /**
     * Retrieve the account details for the given address.
     *
     * @param string $address
     * @return array
     */
    public function getAccount(string $address): array;
}
