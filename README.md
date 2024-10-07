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
    host: 'https://api.shasta.trongrid.io' # Testnet host (Shasta)
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

For additional usage examples, please refer to the `examples` directory.

## License
This project is licensed under the MIT License. See the LICENSE file for details.

## Contributing
Contributions are welcome! Please feel free to submit a pull request or open an issue on GitHub.
