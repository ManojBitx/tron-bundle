<?php

namespace ManojX\TronBundle\Contract;

use ManojX\TronBundle\Exception\TRC20Exception;
use ManojX\TronBundle\Exception\TronAddressException;
use ManojX\TronBundle\Exception\TronException;
use ManojX\TronBundle\Utils\Str;
use ManojX\TronBundle\Utils\Trx;
use ManojX\TronBundle\Wallet\Address\Address;

class TRC20 extends Base
{
    public function __construct(string $contractAddress, string $abi = null)
    {
        parent::__construct($contractAddress, $abi);
    }

    /**
     * @throws TRC20Exception
     */
    public function info(): array
    {
        return [
            'name' => $this->getName(),
            'symbol' => $this->getSymbol(),
            'decimals' => $this->getDecimals(),
            'totalSupply' => $this->getTotalSupply(),
        ];
    }

    public function getName(): string
    {
        if (!$this->name) {
            $name = $this->trigger('name');

            if (!is_string($name)) {
                throw new TRC20Exception('Failed to retrieve TRC20 token name');
            }

            $this->name = Str::sanitize($name);
        }
        return $this->name;
    }

    public function getSymbol(): string
    {
        if (!$this->symbol) {
            $code = $this->trigger('symbol');

            if (!is_string($code)) {
                throw new TRC20Exception('Failed to retrieve TRC20 token symbol');
            }

            $this->symbol = Str::sanitize($code, true);
        }
        return $this->symbol;
    }

    public function getDecimals(): int
    {
        if (!$this->decimals) {
            $decimals = $this->trigger('decimals')->toString();
            $decimals = $decimals ? intval($decimals) : null;

            if (is_null($decimals)) {
                throw new TRC20Exception('Failed to retrieve TRC20 token decimals/scale value');
            }

            $this->decimals = $decimals;
        }
        return $this->decimals;
    }

    public function getTotalSupply(bool $scaled = true): string
    {
        if (!$this->totalSupply) {
            $totalSupply = $this->trigger('totalSupply')->toString();

            if (!is_string($totalSupply) || !preg_match('/^[0-9]+$/', $totalSupply)) {
                throw new TRC20Exception('Failed to retrieve TRC20 token totalSupply');
            }

            $this->totalSupply = $totalSupply;
        }

        if ($scaled) {
            return Trx::fromSun($this->totalSupply, $this->getDecimals());
        }

        return $this->totalSupply;
    }

    public function getBalanceOf(string $address, bool $scaled = true): string
    {
        if (!Address::isValid($address)) {
            throw new TRC20Exception(sprintf('Invalid address "%s"', $address));
        }

        $addr = Address::base58ToHex($address);
        $response = $this->trigger('balanceOf', [$addr]);
        $balance = isset($response['balance']) ? $response['balance']->toString() : null;

        if (!is_string($balance) || !preg_match('/^[0-9]+$/', $balance)) {
            throw new TRC20Exception(sprintf('Failed to retrieve TRC20 token balance of address "%s"', $addr));
        }

        if ($scaled) {
            return Trx::fromSun($balance, $this->getDecimals());
        }

        return $balance;
    }

    /**
     * Transfer TRC20 token to another address
     *
     * @param string $to The recipient address
     * @param string $amount The amount to transfer
     * @param string|null $from The sender address (optional)
     * @return array
     *
     * @throws TRC20Exception
     * @throws TronAddressException
     * @throws TronException
     */
    public function transfer(string $to, string $amount, string $from = null): array
    {
        if (!is_numeric($this->feeLimit) || $this->feeLimit <= 0) {
            throw new TRC20Exception('Fee limit is required. Please set feeLimit using setFeeLimit() method.');
        } elseif ($this->feeLimit > self::MaxFeeLimit) {
            throw new TRC20Exception('Fee limit must not be greater than ' . self::MaxFeeLimit . ' TRX.');
        }

        if (is_null($from)) {
            $from = $this->getWallet()->getAddress();
        }

        $decimals = $this->getDecimals();
        $amountInSun = Trx::toSun($amount, $decimals);

        $inputs = [
            Address::base58ToHex($to),
            $amountInSun,
        ];

        return $this->trigger('transfer', $inputs, $from);
    }

}
