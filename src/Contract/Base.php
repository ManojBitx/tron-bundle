<?php

namespace ManojX\TronBundle\Contract;

use ManojX\TronBundle\Exception\TRC20Exception;
use ManojX\TronBundle\Exception\TronException;
use ManojX\TronBundle\Node\Node;
use ManojX\TronBundle\Utils\Trx;
use ManojX\TronBundle\Wallet\Address\Address as TronAddress;
use Web3\Contracts\Ethabi;
use Web3\Contracts\Types\Address;
use Web3\Contracts\Types\Boolean;
use Web3\Contracts\Types\Bytes;
use Web3\Contracts\Types\DynamicBytes;
use Web3\Contracts\Types\Integer;
use Web3\Contracts\Types\Str;
use Web3\Contracts\Types\Uinteger;

class Base
{
    protected ?string $name = null;

    protected ?string $symbol = null;

    protected ?int $decimals = null;

    protected ?string $totalSupply = null;

    protected string $contractAddress;

    protected array $abi;

    protected int $feeLimit = 20;

    private Ethabi $ethAbi;

    private Node $node;

    const MaxFeeLimit = 500;

    public function __construct(string $contractAddress, string $abi = null)
    {
        if (is_null($abi)) {
            $abi = file_get_contents(__DIR__ . '/ABI/TRC20.json');
        }

        $this->abi = json_decode($abi, true);
        $this->contractAddress = $contractAddress;

        $this->ethAbi = new Ethabi([
            'address' => new Address(),
            'bool' => new Boolean(),
            'bytes' => new Bytes(),
            'dynamicBytes' => new DynamicBytes(),
            'int' => new Integer(),
            'string' => new Str(),
            'uint' => new Uinteger(),
        ]);
    }

    /**
     * Set the ABI for the contract
     *
     * @param array $abi
     *
     * @return void
     */
    public function setAbi(array $abi): void
    {
        $this->abi = $abi;
    }

    /**
     * Set the fee limit for the execution of the contract
     *
     * @param int $feeLimit
     *
     * @return void
     *
     */
    public function setFeeLimit(int $feeLimit): void
    {
        $this->feeLimit = $feeLimit;
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    /**
     * Set the node to use for contract execution
     *
     * @param Node $node
     * @return void
     *
     */
    public function setNode(Node $node): void
    {
        $this->node = $node;
    }

    /**
     * Trigger a function on the contract
     *
     * @param string $function name of the function to trigger
     * @param array|null $params parameters to pass to the function
     * @param string $ownerAddress address of user triggering the function
     * @param int $callValue value to send with the function call
     * @param int $bandwidthLimit bandwidth limit for the function call
     *
     * @return array|mixed|string
     * @throws TRC20Exception
     * @throws TronException
     */
    protected function trigger(
        string $function,
        ?array $params = null,
        string $ownerAddress = Trx::NULL_ADDRESS,
        int    $callValue = 0,
        int    $bandwidthLimit = 0
    )
    {
        if (!$this->node instanceof Node) {
            throw new TRC20Exception('Node is required. Please set node using setNode() method.');
        }

        $functionAbi = [];
        foreach ($this->abi as $item) {
            if (isset($item['name']) && $item['name'] === $function) {
                $functionAbi = $item + ['inputs' => []];
                break;
            }
        }

        if (count($functionAbi) === 0) {
            throw new TronException("Function $function not defined in ABI");
        }

        if (!is_null($params) && !is_array($params)) {
            throw new TronException("Function params must be an array");
        }

        if (is_null($params)) {
            $params = [];
        }

        if (count($functionAbi['inputs']) !== count($params)) {
            throw new TronException("Count of params and abi inputs must be identical");
        }

        $response = $this->triggerNode($functionAbi, $params, $ownerAddress, $callValue, $bandwidthLimit);

        $data = $response['data'];
        if (isset($data['constant_result'])) {
            $decoded = $this->ethAbi->decodeParameters($functionAbi, $data['constant_result'][0]);
            return $decoded[0] ?? $decoded;
        }

        return [
            'success' => $data['result'],
            'data' => $data['transaction'],
        ];
    }

    private function triggerNode(
        array  $functionAbi,
        array  $params,
        string $ownerAddress = Trx::NULL_ADDRESS,
        int    $callValue = 0,
        int    $bandwidthLimit = 0
    ): array
    {
        $inputs = array_column($functionAbi['inputs'], 'type');
        $signature = sprintf('%s(%s)', $functionAbi['name'], implode(',', $inputs));

        $parameters = substr($this->ethAbi->encodeParameters($functionAbi, $params), 2);

        if ($functionAbi['constant']) {
            $payload = [
                'contract_address' => TronAddress::base58ToHex($this->contractAddress),
                'function_selector' => $signature,
                'parameter' => $parameters,
                'owner_address' => $ownerAddress,
            ];
            $response = $this->node->triggerConstantContract($payload);
        } else {
            if (!is_numeric($this->feeLimit) || $this->feeLimit <= 0) {
                throw new TRC20Exception('Fee limit is required. Please set feeLimit using setFeeLimit() method.');
            } elseif ($this->feeLimit > self::MaxFeeLimit) {
                throw new TRC20Exception('Fee limit must not be greater than ' . self::MaxFeeLimit . ' TRX.');
            }
            $payload = [
                'contract_address' => TronAddress::base58ToHex($this->contractAddress),
                'function_selector' => $signature,
                'parameter' => $parameters,
                'owner_address' => TronAddress::base58ToHex($ownerAddress),
                'fee_limit' => Trx::toSun($this->feeLimit),
                'call_value' => $callValue,
                'consume_user_resource_percent' => $bandwidthLimit,
            ];

            $response = $this->node->triggerSmartContract($payload);
        }

        if ($response['success'] === false) {
            throw new TronException('Execution failed: ' . $response['error']['rawMessage']);
        }

        return $response;
    }
}