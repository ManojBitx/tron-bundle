<?php

namespace ManojX\Examples;

use ManojX\TronBundle\TronInterface;

class Transaction
{
    private TronInterface $tron;

    public function __construct(TronInterface $tron)
    {
        $this->tron = $tron;
    }

    public function transferTrx()
    {
        // Private key of the wallet that will send the TRX
        $privateKey = 'your-private-key';
        $wallet = $this->tron->getWallet($privateKey);

        $transaction = $wallet->transaction()
            ->setTo('tron-address-to-send')
            ->setAmount(1);
        $signedTransaction = $transaction->createAndSign();

        $broadcastTransaction = $this->tron->sendRawTransaction($signedTransaction);

        echo '<pre>';
        print_r($signedTransaction);
        print_r($broadcastTransaction);
        echo '</pre>';
        die;
    }


    public function signTransaction()
    {
        // Private key of the wallet that will sign the message
        $privateKey = 'your-private-key';
        $wallet = $this->tron->getWallet($privateKey);

        $messageToSign = 'message-to-sign';
        $signature = $wallet->sign($messageToSign);

        echo '<pre>';
        print_r($signature);
        echo '</pre>';
        die;
    }
}