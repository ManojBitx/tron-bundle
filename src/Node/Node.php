<?php

namespace ManojX\TronBundle\Node;

use ManojX\TronBundle\Exception\TronException;
use ManojX\TronBundle\Provider\HttpProvider;

class Node extends Base implements NodeInterface
{
    const FULL_NODE = 'fullNode';

    const SOLIDITY_NODE = 'solidityNode';

    const EXPLORER = 'explorer';

    private string $nodeToUse = self::FULL_NODE;

    private array $config;

    private string $network;

    private HttpProvider $provider;

    /**
     * @throws TronException
     */
    public function __construct(array $config, string $network)
    {
        $this->config = $config;
        $this->network = $network;
        $this->setNodeToUse($this->nodeToUse);
    }

    public function setNodeToUse(string $nodeToUse): self
    {
        if (!in_array($nodeToUse, [self::FULL_NODE, self::SOLIDITY_NODE, self::EXPLORER])) {
            throw new TronException('Invalid node type provided.');
        }

        if (!in_array($nodeToUse, array_keys($this->config))) {
            throw new TronException('Node not found in the configuration. Please check the configuration file.');
        }

        $this->nodeToUse = $nodeToUse;

        $config = $this->config[$this->nodeToUse];
        $this->provider = new HttpProvider($config['host'], $config['api_key']);
        return $this;
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

    public function accountPermissionUpdate(string $ownerAddress, string $authorizedAddress, string $operations): array
    {
        $payload = [
            "owner_address" => $ownerAddress,
            "actives" => [
                [
                    "type" => 2,
                    "permission_name" => "active",
                    "threshold" => 1,
                    "operations" => $operations,
                    "keys" => [
                        [
                            "address" => $authorizedAddress,
                            "weight" => 1
                        ]
                    ]
                ]
            ],
            "owner" => [
                "type" => 0,
                "permission_name" => "owner",
                "threshold" => 1,
                "keys" => [
                    [
                        "address" => $authorizedAddress,
                        "weight" => 1
                    ]
                ]
            ],
            "visible" => true
        ];
        $response = $this->provider->request('/wallet/accountpermissionupdate', $payload, 'POST');
        return $this->parse($response);
    }

    /**
     * @throws TronException
     */
    public function getTransactionInfoByHash(string $hash): array
    {
        $this->setNodeToUse(self::EXPLORER);
        $response = $this->provider->request('/api/transaction-info?hash=' . $hash);
        if (count($response) <= 0) {
            $response['Error'] = 'Invalid.Hash: Transaction not found or Invalid transaction hash.';
        }
        return $this->parse($response);
    }

    public function getTransactionById(string $txID): array
    {
        $response = $this->provider->request('/wallet/gettransactionbyid', [
            "value" => $txID,
            "visible" => true
        ], 'POST');
        if (count($response) <= 0) {
            $response['Error'] = 'Invalid.Hash: Transaction not found or Invalid transaction hash.';
        }
        return $this->parse($response);
    }

    public function getAccount(string $address): array
    {
        $response = $this->provider->request('/wallet/getaccount', [
            "address" => $address,
            "visible" => true
        ], 'POST');
        if (count($response) <= 0) {
            $response['Error'] = 'Invalid.Address: Transaction not found or Invalid transaction hash.';
        }
        return $this->parse($response);
    }
}
