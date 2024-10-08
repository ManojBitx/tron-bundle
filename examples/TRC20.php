<?php

namespace ManojX\Examples;

use ManojX\TronBundle\TronInterface;

class TRC20
{
    private TronInterface $tron;

    public function __construct(TronInterface $tron)
    {
        $this->tron = $tron;
    }

    public function transferToken()
    {
        $node = $this->tron->getNode();

        $wallet = $this->tron->getWallet('your-private-key');

        $contractAddress = 'TG3XXyExBkPp9nzdajDZsozEu4BkaSJozs'; // USDT Shasta Testnet
        $contract = $wallet->getTrc20($contractAddress);

        $to = 'to-address';
        $tokenAmount = 1;
        $transaction = $contract->transfer($wallet->getAddress(), $to, $tokenAmount);

        $signedTransaction = $wallet->signTransaction($transaction['data']);

        $broadcast = $node->broadcastTransaction($signedTransaction);
        echo '<pre>';
        print_r($broadcast);
        echo '</pre>';
        die;

    }

}