<?php

namespace ManojX\TronBundle\Wallet\Transaction;

use ManojX\TronBundle\Utils\Trx;
use ManojX\TronBundle\Wallet\Address\Address;

trait TransactionTrait
{
    public function isValidTransactionHash(string $hash): bool
    {
        $hash = trim($hash);
        return !empty($hash) && strlen($hash) === 64 && ctype_xdigit($hash);
    }

    public function formatTransactionOfExplorer(array $transaction): array
    {
        $contractData = $transaction['contractData'];
        $datetime = new \DateTimeImmutable('@' . ($transaction['timestamp'] / 1000), new \DateTimeZone('GMT'));
        $confirmed = $transaction['confirmed'];
        if (!$confirmed) {
            $transaction['confirmed'] = $transaction['confirmations'] > 60;
        }

        $formatted = [
            'hash' => $transaction['hash'],
            'type' => $transaction['contract_type'] ?? 'transfer',
            'block' => $transaction['block'],
            'status' => $transaction['contractRet'],
            'timestamp' => $datetime->getTimestamp(),
            'datetime' => $datetime->format('Y-m-d\TH:i:s\Z'),
            'confirmations' => $transaction['confirmations'],
            'confirmed' => $transaction['confirmed'],
        ];

        if ($formatted['status'] === 'SUCCESS') {
            if (isset($transaction['contract_type']) && $transaction['contract_type'] === 'trc20') {
                $transfers = [];
                foreach ($transaction['transfersAllList'] as $transfer) {
                    $transfers[] = self::createTransferInfo($transfer);
                }
                $formatted['transfers'] = $transfers;
            } else {
                $formatted['fromAddr'] = $transaction['ownerAddress'];
                $formatted['toAddr'] = $transaction['toAddress'];
                $formatted['amount'] = Trx::fromSun($contractData['amount']);
                $formatted['amountSun'] = $contractData['amount'];
                $formatted['name'] = 'Tron';
                $formatted['type'] = 'transfer';
                $formatted['symbol'] = 'TRX';
                $formatted['decimals'] = 6;
            }
        }

        return $formatted;
    }

    private static function createTransferInfo($transfer): array
    {
        return [
            'name' => $transfer['name'],
            'type' => strtoupper($transfer['tokenType']),
            'symbol' => $transfer['symbol'],
            'decimals' => $transfer['decimals'],
            'fromAddr' => $transfer['from_address'],
            'toAddr' => $transfer['to_address'],
            'amount' => Trx::fromSun($transfer['amount_str']),
            'amountSun' => (int)$transfer['amount_str'],
            'contractAddr' => $transfer['contract_address'],
        ];
    }
}