<?php

namespace ManojX\TronBundle\Node;

use ManojX\TronBundle\Exception\TronException;
use ManojX\TronBundle\Provider\HttpProvider;

class Node extends Base implements NodeInterface
{
    private string $network;

    private HttpProvider $provider;

    /**
     * @throws TronException
     */
    public function __construct(array $httpConfig, string $network)
    {
        $this->network = $network;
        $this->provider = new HttpProvider($httpConfig['host']);
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function getCurrentBlock(): array
    {
        return $this->provider->request('/wallet/getnowblock');
    }

    public function broadcastTransaction(array $signedTransaction): array
    {
        $response = $this->provider->request('/wallet/broadcasttransaction', $signedTransaction, 'POST');
        return $this->parse($response);
    }

    public function createTransaction(array $transaction): array
    {
        $response = $this->provider->request('/wallet/createtransaction', $transaction, 'POST');
        return $this->parse($response);
    }

    public function triggerConstantContract(array $contract): array
    {
        $response = $this->provider->request('/wallet/triggerconstantcontract', $contract, 'POST');
        return $this->parse($response);
    }

    public function triggerSmartContract(array $contract): array
    {
        $response = $this->provider->request('/wallet/triggersmartcontract', $contract, 'POST');
        return $this->parse($response);
    }
}
