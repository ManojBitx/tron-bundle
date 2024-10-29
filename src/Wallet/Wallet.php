<?php

namespace ManojX\TronBundle\Wallet;

use Elliptic\EC;
use ManojX\TronBundle\Contract\Token\USDT;
use ManojX\TronBundle\Contract\TRC20;
use ManojX\TronBundle\Exception\TronAddressException;
use ManojX\TronBundle\Exception\TronException;
use ManojX\TronBundle\Node\NodeInterface;
use ManojX\TronBundle\Wallet\Address\Address;
use ManojX\TronBundle\Wallet\Transaction\Transaction;

class Wallet implements WalletInterface
{
    use WalletTrait;

    private EC $ec;

    private ?NodeInterface $node = null;

    private ?Address $account = null;

    /**
     * @throws TronAddressException
     */
    public function __construct(?string $privateKey = null, ?NodeInterface $node = null)
    {
        $this->ec = new EC('secp256k1');

        if ($privateKey) {
            $publicKey = $this->ec->keyFromPrivate($privateKey)->getPublic('hex');
            $this->account = new Address([
                'public_key' => $publicKey,
                'private_key' => $privateKey,
            ]);
        }
        $this->node = $node;
    }

    /**
     * Get the account address.
     *
     * @return Address The Address instance representing the wallet's account.
     */
    public function getAccount(): Address
    {
        return $this->account;
    }

    /**
     * Retrieve the wallet address.
     *
     * @param bool $hex Whether to return the address in hexadecimal format.
     * @return string The wallet address.
     */
    public function getAddress(bool $hex = false): string
    {
        if ($hex) {
            return $this->account->getAddressHex();
        }

        return $this->account->getAddress();
    }

    public function setAccount(Address $account): self
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Get the node instance used for blockchain interactions.
     *
     * @return NodeInterface The Node instance.
     * @throws TronAddressException If the node is not provided.
     */
    public function getNode(): NodeInterface
    {
        if (!$this->node) {
            throw new TronAddressException('Please provide a node to get the node.');
        }
        return $this->node;
    }

    /**
     * Create a new account and activate it.
     *
     * @return array
     *
     * @throws TronException
     */
    public function createNewAccount(): array
    {
        if (!$this->account) {
            throw new TronException('Account not set');
        }

        try {
            $newAccount = Address::create();
            $data = $this->activateAccount($newAccount);
            if ($data['success']) {
                return [
                    'success' => true,
                    'data' => $newAccount,
                ];
            }
            throw new TronException('Failed [createNewAccount]: ' . $data['error']['rawMessage']);
        } catch (\Exception $e) {
            throw new TronException('Failed [createNewAccount]: ' . $e->getMessage());
        }
    }

    /**
     * Activate an account.
     *
     * @param Address $address
     * @return array
     *
     * @throws TronException
     */
    public function activateAccount(Address $address): array
    {
        if (!$this->account) {
            throw new TronException('Account not set');
        }

        $node = $this->getNode();
        $response = $node->createAccount($this->account->getAddress(), $address->getAddress());
        if ($response['success']) {
            $signedTransaction = $this->signTransaction($response['data']);
            $broadcastResponse = $node->broadcastTransaction($signedTransaction);
            if ($broadcastResponse['success']) {
                return [
                    'success' => true,
                    'data' => $broadcastResponse['data'],
                ];
            }
            throw new TronException('Failed [activateAccount]: ' . $broadcastResponse['error']['rawMessage']);
        }

        if (strpos($response['error']['rawMessage'], 'Account has existed') !== false) {
            return [
                'success' => true,
                'message' => 'Account already exists',
                'data' => [
                    'ownerAddress' => $this->account->getAddress(),
                    'address' => $address->getAddress(),
                ],
            ];
        }

        throw new TronException('Failed [activateAccount]: ' . $response['error']['rawMessage']);
    }

    /**
     * Get the native balance of the wallet.
     *
     * @param string|null $address
     * @return array
     *
     * @throws TronAddressException
     */
    public function getNativeBalance(?string $address = null): array
    {
        if ($address) {
            $this->account = new Address(['address' => $address]);
        }
        $node = $this->getNode();
        $response = $node->getAccount($this->getAddress());
        if ($response['success']) {
            return [
                'success' => true,
                'data' => $this->formatNativeBalance($response['data']),
            ];
        }

        throw new TronAddressException('Failed to get balance due to: ' . $response['error']['rawMessage']);
    }

    /**
     * Create a TRC20 token instance.
     *
     * @param string $contractAddress The TRC20 token contract address.
     * @return TRC20 The TRC20 token instance.
     * @throws TronAddressException If the contract address is invalid.
     */
    public function getTrc20(string $contractAddress): TRC20
    {
        $trc20 = new TRC20($contractAddress);
        $trc20->setWallet($this);
        return $trc20;
    }

    /**
     * Create a new Transaction instance with the current Wallet and Node connection.
     *
     * @return Transaction A new Transaction instance with Wallet and Node connection.
     */
    public function transaction(): Transaction
    {
        $transaction = new Transaction();
        $transaction->setWallet($this);
        $transaction->setNode($this->node);
        return $transaction;
    }

    /**
     * Create a USDT token instance.
     *
     * @param string|null $contractAddress Optional contract address for USDT.
     * @param array|null $abi Optional ABI definition for the contract.
     *
     * @return USDT The USDT token instance.
     *
     * @throws TronException If there is an error while creating the token instance.
     */
    public function getUsdt(?string $contractAddress = null, ?array $abi = null): USDT
    {
        $network = $this->getNode()->getNetwork();
        $usdt = new USDT($network, $contractAddress, $abi);
        $usdt->setWallet($this);
        return $usdt;
    }

    /**
     * Sign a message using the wallet's private key.
     *
     * @param string $message The message to be signed.
     * @return string The signature.
     */
    public function sign(string $message): string
    {
        $sign = $this->ec->sign($message, $this->account->getPrivateKey(), ['canonical' => false]);

        $r = $sign->r->toString('hex');
        $s = $sign->s->toString('hex');

        return $r . $s . bin2hex(chr($sign->recoveryParam)); // Combine them in your desired format
    }

    /**
     * Sign a transaction using the wallet's private key.
     *
     * @param array $transaction The transaction data to be signed.
     * @return array The signed transaction.
     */
    public function signTransaction(array $transaction): array
    {
        $signature = $this->sign($transaction['txID']);
        $transaction['signature'] = $signature;
        return $transaction;
    }

    /**
     * Update the permissions of the wallet owner address.
     *
     * @param string $authorizedAddress The address to be authorized.
     * @param string $operations The type of operations to allow.
     * @return array The response from the blockchain after broadcasting the transaction.
     * @throws TronAddressException If the update fails.
     */
    public function updatePermission(string $authorizedAddress, string $operations = Operations::FULL): array
    {
        $node = $this->getNode();

        $ownerAddress = $this->getAddress();
        $response = $node->accountPermissionUpdate($ownerAddress, $authorizedAddress, $operations);
        if ($response['success']) {
            $signedTransaction = $this->signTransaction($response['data']);
            return $this->getNode()->broadcastTransaction($signedTransaction);
        }
        throw new TronAddressException('Failed to update permission due to: ' . $response['message']);
    }


    /**
     * Reset permissions of the owner address.
     *
     * @param string $ownerAddress The owner address whose permissions are to be reset.
     * @return array The response from the blockchain after broadcasting the transaction.
     * @throws TronAddressException If the reset fails.
     */
    public function resetPermissions(string $ownerAddress): array
    {
        $node = $this->getNode();

        $response = $node->accountPermissionUpdate($ownerAddress, $ownerAddress, Operations::FULL);
        if ($response['success']) {
            $signedTransaction = $this->signTransaction($response['data']);
            return $this->getNode()->broadcastTransaction($signedTransaction);
        }
        throw new TronAddressException('Failed to update permission due to: ' . $response['message']);
    }

}