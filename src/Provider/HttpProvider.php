<?php

namespace ManojX\TronBundle\Provider;

use ManojX\TronBundle\Exception\TronException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpProvider implements HttpProviderInterface
{
    /**
     * HTTP Client Handler
     *
     * @var HttpClientInterface.
     */
    private HttpClientInterface $httpClient;

    private ?string $authBasic = null;

    private ?string $authBearer = null;

    private ?array $headers = null;

    /**
     * @throws TronException
     */
    public function __construct(string $host, ?string $apiKey = null)
    {
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];

        if ($apiKey) {
            $options['headers']['TRON-PRO-API-KEY'] = $apiKey;
        }

        if ($this->authBasic && $this->authBearer) {
            throw new TronException('You can only use one of the authBasic or authBearer options');
        }

        if ($this->authBearer) {
            $options['auth_basic'] = $this->authBasic;
        }

        if ($this->authBearer) {
            $options['auth_bearer'] = $this->authBearer;
        }

        if ($this->headers) {
            $options['headers'] = array_merge($options['headers'], $this->headers);
        }

        $this->httpClient = HttpClient::createForBaseUri($host, $options);
    }


    public function request(string $endpoint, array $payload = [], string $method = 'GET'): array
    {
        $response = $this->httpClient->request($method, $endpoint, [
            'json' => $payload,
        ]);

        return $response->toArray();
    }

    public function setAuthBasic(?string $authBasic): void
    {
        $this->authBasic = $authBasic;
    }

    public function setAuthBearer(?string $authBearer): void
    {
        $this->authBearer = $authBearer;
    }

    public function setHeaders(?array $headers): void
    {
        $this->headers = $headers;
    }
}