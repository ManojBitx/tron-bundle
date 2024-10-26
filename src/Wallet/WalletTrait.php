<?php

namespace ManojX\TronBundle\Wallet;

use ManojX\TronBundle\Utils\Trx;

trait WalletTrait
{
    /**
     * Formats the native balance data into a standardized structure.
     *
     * @param array<string, mixed> $data Raw balance data containing address, balance, and account_resource
     * @return array<string, mixed> Formatted balance data
     * @throws \InvalidArgumentException If required data is missing or invalid
     */
    protected function formatNativeBalance(array $data): array
    {
        $requiredKeys = ['address', 'balance', 'account_resource'];
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                throw new \InvalidArgumentException("Missing required key: {$key}");
            }
        }

        if (!is_numeric($data['balance'])) {
            throw new \InvalidArgumentException('Balance must be a numeric value');
        }

        return [
            'address' => $data['address'],
            'balance' => Trx::fromSun($data['balance']),
            'balanceSun' => $data['balance'],
            'account_resource' => $data['account_resource'],
        ];
    }
}