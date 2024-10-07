<?php

namespace ManojX\TronBundle\Node;

use ManojX\TronBundle\Exception\TronException;
use ManojX\TronBundle\Provider\HttpProvider;

class Node extends Base implements NodeInterface
{
    private HttpProvider $provider;

    /**
     * @throws TronException
     */
    public function __construct(array $httpConfig = [])
    {
        if (!isset($httpConfig['host'])) {
            $httpConfig['host'] = 'https://api.shasta.trongrid.io';
        }
        $this->provider = new HttpProvider($httpConfig['host']);
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
}
