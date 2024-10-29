<?php

namespace ManojX\TronBundle\Wallet;

use ManojX\TronBundle\Contract\Token\USDT;
use ManojX\TronBundle\Contract\TRC20;
use ManojX\TronBundle\Exception\TronAddressException;
use ManojX\TronBundle\Node\NodeInterface;
use ManojX\TronBundle\Wallet\Address\Address;

interface WalletInterface
{
    /**
     * Retrieves the wallet's account details.
     *
     * @return Address Returns an instance of the Address class representing the wallet's account.
     */
    public function getAccount(): Address;

    /**
     * Sets the wallet's account details.
     *
     * @param Address $account The account details to set.
     * @return Wallet
     */
    public function setAccount(Address $account): WalletInterface;

    /**
     * Retrieves the wallet's address in either base58 or hexadecimal format.
     *
     * @param bool $hex If true, returns the address in hexadecimal format. Otherwise, returns in base58 format.
     *
     * @return string The wallet's address.
     */
    public function getAddress(bool $hex = false): string;

    /**
     * Gets the Node instance the wallet is connected to.
     *
     * @return NodeInterface An instance of NodeInterface, representing the node the wallet interacts with.
     */
    public function getNode(): NodeInterface;

    /**
     * Creates a new wallet account and activates it.
     *
     * @return array
     */
    public function createNewAccount(): array;

    /**
     * Activates the wallet account.
     *
     * @param Address $address
     * @return array
     */
    public function activateAccount(Address $address): array;

    /**
     * Retrieves a TRC20 contract instance for interacting with a specific contract address.
     *
     * @param string $contractAddress The contract address of the TRC20 token.
     *
     * @return TRC20 An instance of the TRC20 contract.
     */
    public function getTrc20(string $contractAddress): TRC20;

    /**
     * Retrieves a USDT contract instance for interacting with the USDT token.
     *
     * @param string|null $contractAddress (Optional) The contract address of the USDT token. If null, defaults to the standard USDT contract.
     * @param array|null $abi (Optional) The ABI (Application Binary Interface) of the USDT contract. If null, defaults to the standard ABI.
     *
     * @return USDT An instance of the USDT contract.
     */
    public function getUsdt(?string $contractAddress = null, ?array $abi = null): USDT;

    /**
     * Signs a message using the wallet's private key.
     *
     * @param string $message The message to sign.
     *
     * @return string The signed message.
     */
    public function sign(string $message): string;

    /**
     * Signs a transaction using the wallet's private key.
     *
     * @param array $transaction The transaction details that need to be signed.
     *
     * @return array The signed transaction details.
     */
    public function signTransaction(array $transaction): array;

    /**
     * Updates the permissions for an authorized address on the wallet.
     *
     * @param string $authorizedAddress The address being authorized for specific operations.
     * @param string $operations The operations for which the address is authorized (e.g., sending tokens, freezing tokens).
     *
     * @return array The result of the permission update operation.
     */
    public function updatePermission(string $authorizedAddress, string $operations): array;

    /**
     * Resets the wallet's permissions to the owner address.
     *
     * This operation can only be executed by an address with full owner permissions.
     *
     * @param string $ownerAddress The wallet owner’s address whose permissions will be reset.
     *
     * @return array The result of the permission reset operation.
     */
    public function resetPermissions(string $ownerAddress): array;
}
