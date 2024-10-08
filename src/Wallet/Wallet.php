<?php

namespace ManojX\TronBundle\Wallet;

use Elliptic\EC;
use ManojX\TronBundle\Contract\TRC20;
use ManojX\TronBundle\Exception\TronAddressException;
use ManojX\TronBundle\Node\NodeInterface;
use ManojX\TronBundle\Wallet\Address\Address;
use ManojX\TronBundle\Wallet\Transaction\Transaction;

class Wallet implements WalletInterface
{

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

    public function getAddress(bool $hex = false): string
    {
        if (!$this->account) {
            throw new TronAddressException('Please provide a private key to get the address.');
        }

        if ($hex) {
            return $this->account->getAddressHex();
        }

        return $this->account->getAddress();
    }

    /**
     * Get the node instance
     * @return NodeInterface
     * @throws TronAddressException
     */
    public function getNode(): NodeInterface
    {
        if (!$this->node) {
            throw new TronAddressException('Please provide a node to get the node.');
        }
        return $this->node;
    }

    public function getTrc20(string $contractAddress): TRC20
    {
        $trc20 = new TRC20($contractAddress);
        $trc20->setNode($this->node);
        return $trc20;
    }

    /**
     * Initialize a new transaction
     */
    public function initTransaction(): Transaction
    {
        return new Transaction($this);
    }

    public function sign(string $message): string
    {
        $sign = $this->ec->sign($message, $this->account->getPrivateKey(), ['canonical' => false]);

        $r = $sign->r->toString('hex');
        $s = $sign->s->toString('hex');

        return $r . $s . bin2hex(chr($sign->recoveryParam)); // Combine them in your desired format
    }

    public function signTransaction(array $transaction): array
    {
        $signature = $this->sign($transaction['txID']);
        $transaction['signature'] = $signature;
        return $transaction;
    }

    /**
     * Create a new address
     *
     * @return Address
     *
     * @throws TronAddressException
     */
    public function createNewAddress(): Address
    {
        $keyPair = $this->ec->genKeyPair();
        $privateKey = $keyPair->getPrivate('hex');
        $publicKey = $keyPair->getPublic('hex');

        return new Address([
            'public_key' => $publicKey,
            'private_key' => $privateKey,
        ]);
    }

}