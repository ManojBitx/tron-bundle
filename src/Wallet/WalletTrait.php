<?php

namespace ManojX\TronBundle\Wallet;

use ManojX\TronBundle\Utils\Trx;

trait WalletTrait
{
    protected function formatNativeBalance($data): array
    {
        return [
            'address' => $data['address'],
            'balance' => Trx::fromSun($data['balance']),
            'balanceSun' => $data['balance'],
            'account_resource' => $data['account_resource'],
        ];

    }
}