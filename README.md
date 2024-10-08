A Symfony bundle that makes it easy to interact with the Tron Blockchain. It allows you to manage wallets, create and sign transactions, broadcast them to the network, and handle tasks like generating and validating addresses. This bundle simplifies working with Tron in Symfony applications.

## Features

- Create and manage Tron wallets
- Sign transactions and broadcast them to the Tron network
- Create new Tron addresses and validate them
- Easy integration with Symfony applications

## Installation

To install the Tron bundle, use Composer:

```bash
composer require manojx/tron-bundle
```

## Requirements
- PHP ^7.4 || ^8.0
- Symfony ^5.4 || ^6.1 || ^7.0
- simplito/elliptic-php ^1.0
- kornrunner/keccak ^1.1

## Configuration
You can configure the Tron node endpoint in your Symfony configuration:
```yaml
# config/packages/tron.yaml
tron:
    default_network: shasta
    networks:
        mainnet:
            http:
                host: 'https://api.trongrid.io'
                api_key: 'your_api_key'
        shasta:
            http:
                host: 'https://api.shasta.trongrid.io'
                api_key: 'your_api_key'

        nile:
            http:
                host: 'https://nile.trongrid.io'
                api_key: 'your_api_key'
```

## Usage
Creating a Tron Wallet

You can use the Tron service to create a new wallet or access an existing wallet.

```php
use ManojX\TronBundle\TronInterface;

class WalletService
{
    private TronInterface $tron;

    public function __construct(TronInterface $tron)
    {
        $this->tron = $tron;
    }

    public function createWallet()
    {
        $wallet = $this->tron->getWallet();

        $address = $wallet->createNewAddress();
        echo 'Address: ' . $address->getAddress();
        echo 'Private Key: ' . $address->getPrivateKey();
    }
}
```

## Sending TRX

You can initialize a transaction and send TRX using the wallet's private key.

```php
use ManojX\TronBundle\TronInterface;

class TransactionService
{
    private TronInterface $tron;

    public function __construct(TronInterface $tron)
    {
        $this->tron = $tron;
    }

    public function sendTrx()
    {
        $wallet = $this->tron->getWallet('your-private-key');

        $transaction = $wallet->initTransaction();
        $transaction->setTo('receiver-tron-address');
        $transaction->setAmount(1); // Amount in TRX
        $signedTransaction = $transaction->createAndSign();

        $node = $this->tron->getNode();
        $response = $node->broadcastTransaction($signedTransaction);

        return $response;
    }
}
```

## USDT & Contract Interaction

You can interact with USDT and other smart contracts on the Tron network.

USDT Contract Address: [TG3XXyExBkPp9nzdajDZsozEu4BkaSJozs](https://shasta.tronscan.org/#/token20/TG3XXyExBkPp9nzdajDZsozEu4BkaSJozs) (Shasta Testnet)

```php
use ManojX\TronBundle\TronInterface;

class TransactionService
{
    private TronInterface $tron;

    public function __construct(TronInterface $tron)
    {
        $this->tron = $tron;
    }

    public function sendUsdt()
    {
        $wallet = $this->tron->getWallet('your-private-key');

        $usdt = $wallet->getUsdt();
        $transaction = $usdt->transfer('to-address', 1);

        $signedTransaction = $wallet->signTransaction($transaction['data']);

        $response = $node->broadcastTransaction($signedTransaction);
        
        return $response;
    }
}
```

For additional usage examples, please refer to the `examples` directory.

## License
This project is licensed under the MIT License. See the LICENSE file for details.

## Contributing
Contributions are welcome! Please feel free to submit a pull request or open an issue on GitHub.
